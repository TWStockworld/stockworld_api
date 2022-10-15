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
use App\Models\StockData;

class CalculateStockJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries = 1;
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
        TestStock::create(['test1' => 'first']);
        // $stockAA = StockName::where('stock_id', 1101)->get();
        $stocks = StockName::all();
        // $stockaa = StockName::where(['stock_category_id' => $this->stock_category_id])->get();
        $stock_data_temp = collect();
        $out_stock_list_up = collect();
        $out_stock_list_down = collect();
        $cou = 0;

        $add_diff_enddate = date("Y-m-d", strtotime($this->enddate . '+ 15 days'));

        $stock_data_temp = StockData::where('date', '>=', $this->startdate)->where('date', '<=', $add_diff_enddate)->get()->groupby('stock_name_id');


        $stockA_id = 0;
        $stockA_name_id = 0;
        $stockB_id = 0;
        $stockB_name_id = 0;

        foreach ($stocks as $stockA) {
            $stockA_id = $stockA->stock_id;
            $stockA_name_id = StockName::get_stock_name_id($stockA_id);
            $cou++;

            //整理
            if (count($out_stock_list_up) > 30) {
                $out_stock_list_up = $out_stock_list_up->sortByDesc('up')->values()->take(30);
            }
            if (count($out_stock_list_down) > 30) {
                $out_stock_list_down = $out_stock_list_down->sortByDesc('down')->values()->take(30);
            }
            foreach ($stocks as $stockB) {
                $stockB_id = $stockB->stock_id;
                $stockB_name_id = StockName::get_stock_name_id($stockB_id);

                if ($stockA_id != $stockB_id) {
                    $breakkey = 0;
                    $breakkey = self::cal_two_stock($this->startdate, $this->enddate, $this->diff, $stockA_name_id, $stockB_name_id, $stock_data_temp, $out_stock_list_up, $out_stock_list_down);
                    if ($breakkey == 401) {
                        break 1;
                    }
                }
            }
            TestStock::create(['test1' => $cou, 'test2' => $stockA_id]);
        }


        $out_stock_list_up = $out_stock_list_up->sortByDesc('up')->values();
        StockCalculate::insert($out_stock_list_up->toArray());

        $out_stock_list_down = $out_stock_list_down->sortByDesc('down')->values();;
        StockCalculate::insert($out_stock_list_down->toArray());
    }

    public function cal_two_stock($startdate, $enddate, $diff, $stockA_name_id, $stockB_name_id, $stock_data_temp, $out_stock_list_up, $out_stock_list_down)
    {
        $stockA_datas = $stock_data_temp->get($stockA_name_id);
        $stockB_datas = $stock_data_temp->get($stockB_name_id);
        if ($stockA_datas != null) {
            $stockA_datas = $stockA_datas->where('date', '<=', $enddate);
            if ($stockA_datas->count() != 0) {
                if (((strtotime($stockA_datas[0]['date']) - strtotime($startdate)) / (60 * 60 * 24)) < 15) { //15天寬限
                    if ($stockB_datas != null && $stockB_datas->count() != 0) {
                        if (((strtotime($stockB_datas[0]['date']) - strtotime($startdate)) / (60 * 60 * 24)) < 15) { //15天寬限
                            $zero_diff_up = 0;
                            $zero_diff_down = 0;
                            for ($c_diff = 0; $c_diff <= $diff; $c_diff++) {
                                $diff_stockB_datas = $stockB_datas->skip($c_diff)->take($stockA_datas->count())->values();

                                if ($stockA_datas->count() == $diff_stockB_datas->count()) {

                                    $a = 0;
                                    $b = 0;
                                    $c = 0;
                                    $d = 0;

                                    foreach ($stockA_datas as $key => $v) {
                                        $stockA_day_change = $stockA_datas[$key]['day_change'];
                                        $stockB_day_change = $diff_stockB_datas[$key]['day_change'];
                                        if ($stockA_day_change > 0 &&  $stockB_day_change > 0) {
                                            $a++;
                                        } else if ($stockA_day_change > 0 &&  $stockB_day_change <= 0) {
                                            $b++;
                                        } else if ($stockA_day_change <= 0 &&  $stockB_day_change > 0) {
                                            $c++;
                                        } else if ($stockA_day_change <= 0 &&  $stockB_day_change <= 0) {
                                            $d++;
                                        }
                                    }
                                    //A漲 B x天後 也跟著漲
                                    $up = round($a / ($a + $b), 2) * 100;
                                    //A跌B x天後 也跟著跌
                                    $down = round($d / ($c + $d), 2) * 100;
                                    if ($c_diff == 0) {
                                        $zero_diff_up = $up;
                                        $zero_diff_down = $down;
                                    } else {
                                        $result = [
                                            'group_id' => 1, 'stockA_name_id' => $stockA_name_id, 'stockB_name_id' => $stockB_name_id, 'diff' => $c_diff,
                                            'up' => $up, 'down' => $down, 'startdate' => $startdate, 'enddate' => $enddate,
                                            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')
                                        ];
                                        if ($up > $zero_diff_up + 5) {
                                            $out_stock_list_up->push($result);
                                        }
                                        if ($down > $zero_diff_down + 5) {
                                            $out_stock_list_down->push($result);
                                        }
                                    }
                                } else {
                                    return 0;
                                }
                            }
                        }
                    }
                } else {
                    return 401;
                }
            } else {
                return 401;
            }
        } else {
            return 401;
        }
    }
}
