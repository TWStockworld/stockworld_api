<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\StockName;
use App\Models\StockCalculate;
use App\Models\TestStock;

class CalculateStockJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries = 2;
    protected $startdate, $enddate, $diff, $stock_category_id;

    public function __construct($startdate, $enddate, $diff, $stock_category_id)
    {
        $this->startdate = $startdate;
        $this->enddate = $enddate;
        $this->diff = $diff;
        $this->stock_category_id = $stock_category_id;
        //
    }
    public function handle()
    {
        $stockAA = StockName::where(['stock_id' => 1101])->get();
        $stocks = StockName::all();
        // $stocks = StockName::where(['stock_category_id' => $this->stock_category_id])->get();
        $stock_data_temp = collect();
        $out_stock_list = [];
        $cou = 0;
        foreach ($stockAA as $stockA) {
            $cou++;
            foreach ($stocks as $stockB) {
                $stockA_id = $stockA->stock_id;
                $stockB_id = $stockB->stock_id;
                if ($stockA_id != $stockB_id) {
                    for ($c_diff = 1; $c_diff <= $this->diff; $c_diff++) {
                        $breakkey = 0;
                        list($up, $down, $breakkey) = self::cal_two_stock($this->startdate, $this->enddate, $c_diff, $stockA_id, $stockB_id, $stock_data_temp);
                        if ($breakkey == 401) {
                            break 2;
                        } else {
                            $result = [
                                'group_id' => 1, 'stockA_name_id' => StockName::get_stock_name_id($stockA_id), 'stockB_name_id' => StockName::get_stock_name_id($stockB_id), 'diff' => $c_diff,
                                'up' => $up, 'down' => $down, 'startdate' => $this->startdate, 'enddate' => $this->enddate,
                                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')
                            ];
                            array_push($out_stock_list, $result);
                        }
                    }
                    TestStock::create(['test1' => $cou, 'test2' => $stockA_id, 'test3' => $stockB_id]);
                }
            }
        }
        usort($out_stock_list, function ($a, $b) {
            return $a['up'] < $b['up'];
        });
        for ($count = 0; $count < 30; $count++) {
            StockCalculate::insert($out_stock_list[$count]);
        }
        usort($out_stock_list, function ($a, $b) {
            return $a['down'] < $b['down'];
        });
        for ($count = 0; $count < 30; $count++) {
            StockCalculate::insert($out_stock_list[$count]);
        }
    }

    public function cal_two_stock($startdate, $enddate, $diff, $stockA, $stockB, $stock_data_temp)
    {
        if (!$stock_data_temp->has($stockA)) {
            $add_diff_enddate = date("Y-m-d", strtotime($enddate . '+ 15 days'));
            $stockA_datas = StockName::where(['stock_id' => $stockA])->first()->StockData->where('date', '>=', $startdate)->where('date', '<=', $add_diff_enddate)->values();
            $stock_data_temp->put($stockA, $stockA_datas);
        }
        if (!$stock_data_temp->has($stockB)) {
            $add_diff_enddate = date("Y-m-d", strtotime($enddate . '+ 15 days'));
            $stockB_datas = StockName::where(['stock_id' => $stockB])->first()->StockData->where('date', '>=', $startdate)->where('date', '<=', $add_diff_enddate)->values();
            $stock_data_temp->put($stockB, $stockB_datas);
        }
        $stockA_datas = $stock_data_temp->get($stockA)->where('date', '<=', $enddate);
        $stockB_datas = $stock_data_temp->get($stockB);
        if ($stockA_datas->count() != 0) {
            if (((strtotime($stockA_datas[0]['date']) - strtotime($startdate)) / (60 * 60 * 24)) < 15) { //15天寬限
                if ($stockB_datas->count() != 0) {
                    if (((strtotime($stockB_datas[0]['date']) - strtotime($startdate)) / (60 * 60 * 24)) < 15) { //15天寬限
                        $stockB_datas = $stockB_datas->skip($diff)->take($stockA_datas->count())->values();
                        if ($stockA_datas->count() == $stockB_datas->count()) {
                            $a = 0;
                            $b = 0;
                            $c = 0;
                            $d = 0;
                            $days = $stockA_datas->count();

                            for ($i = 0; $i < $days; $i++) {
                                if ($stockA_datas[$i]['day_change'] > 0 && $stockB_datas[$i]['day_change'] > 0) {
                                    $a++;
                                } else if ($stockA_datas[$i]['day_change'] > 0 && $stockB_datas[$i]['day_change'] <= 0) {
                                    $b++;
                                } else if ($stockA_datas[$i]['day_change'] <= 0 && $stockB_datas[$i]['day_change'] > 0) {
                                    $c++;
                                } else if ($stockA_datas[$i]['day_change'] <= 0 && $stockB_datas[$i]['day_change'] <= 0) {
                                    $d++;
                                }
                            }
                            //A漲 B x天後 也跟著漲
                            $up = round($a / ($a + $b), 2) * 100;
                            //A跌B x天後 也跟著跌
                            $down = round($d / ($c + $d), 2) * 100;
                            return [$up, $down, 0];
                        } else {
                            return [0, 0, 0];
                        }
                    }
                }
            } else {
                return [0, 0, 401];
            }
        } else {
            return [0, 0, 401];
        }
    }
}
