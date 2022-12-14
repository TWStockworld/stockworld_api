<?php

namespace App\Repositories;

use App\Models\Bulletin;
use App\Models\StockCalculate;
use App\Models\StockCategory;
use App\Models\StockData;
use App\Models\StockName;
use App\Models\StockSpecialKindDetail;

use App\Jobs\CalculateStockJob;

class CalStockRepository
{
    protected $user;

    // public function __construct(User $user, StockName $stockName, StockCategory $stockCategory,StockData $stockData)
    // {
    //     $this->user = $user;
    //     $this->stockName = $stockName;
    //     $this->stockCategory = $stockCategory;
    //     $this->stockData = $stockData;
    // }

    public function cal_all_stock_probability($data)
    {
        $startdate = $data->startdate; //'2021-01-01';
        $enddate = $data->enddate; //'2021-12-01';
        $diff = $data->diff;
        $stock_category_id = $data->stock_category_id;

        if ($stock_category_id != null) {
            CalculateStockJob::dispatch($startdate, $enddate, $diff, $stock_category_id);

            return response()->json(['success' => '已自動開始計算，請稍等'], 200);
        }
    }
    public function cal_stock_withoutdiff($data)
    {
        $startdate = $data->startdate; //'2021-01-01';
        $enddate = $data->enddate; //'2021-12-01';

        $stockA_id = $data->stockA_id;
        $stockB_id = $data->stockB_id;
        $stockA_name = StockName::get_stock_name($stockA_id);
        $stockB_name = StockName::get_stock_name($stockB_id);

        list(
            $out_up, $up_diff, $up_list, $out_up_stockA_datas, $out_up_stockB_datas,
            $out_down, $down_diff, $down_list, $out_down_stockA_datas, $out_down_stockB_datas
        ) = self::cal_two_stock_withoutdiff($startdate, $enddate, $stockA_id, $stockB_id);

        $up_sendresult = $stockA_name . "(" . $stockA_id . "黃線)" . "漲，" . $stockB_name . "(" . $stockB_id . "藍線)" . $up_diff . "天後 也跟著漲" . $out_up . "%";
        $down_sendresult = $stockA_name . "(" . $stockA_id . "黃線)" . "跌，" . $stockB_name . "(" . $stockB_id . "藍線)" . $down_diff . "天後 也跟著跌" . $out_down . "%";

        return response()->json([
            'up_sendresult' => $up_sendresult, 'down_sendresult' => $down_sendresult,
            'up_list' => $up_list, 'down_list' => $down_list,
            'out_up_stockA_datas' => $out_up_stockA_datas, 'out_up_stockB_datas' =>  $out_up_stockB_datas,
            'out_down_stockA_datas' => $out_down_stockA_datas, 'out_down_stockB_datas' =>  $out_down_stockB_datas
        ], 200);
    }
    public function cal_two_stock_withoutdiff($startdate, $enddate, $stockA, $stockB)
    {
        $up_list = collect();
        $down_list = collect();
        $out_up = 0;
        $out_down = 0;
        $out_up_stockA_datas = '';
        $out_up_stockB_datas = '';
        $out_down_stockA_datas = '';
        $out_down_stockB_datas = '';
        $up_diff = 0;
        $down_diff = 0;
        $stockA_datas = StockName::where(['stock_id' => $stockA])->first()->StockData->where('date', '>=', $startdate)->where('date', '<=', $enddate)->values();
        if (StockName::where(['stock_id' => $stockB])->first()->StockData->where('date', $stockA_datas[0]['date'])->count() != 0) {
            for ($diff = 0; $diff <= 10; $diff++) {
                $stockB_datas = StockName::where(['stock_id' => $stockB])->first()->StockData->where('date', '>=', $startdate)->collect()->skip($diff)->take($stockA_datas->count())->values();
                if ($stockA_datas->count() != 0 && $stockB_datas->count() != 0 && ($stockA_datas->count() == $stockB_datas->count())) {
                    $a = 0;
                    $b = 0;
                    $c = 0;
                    $d = 0;
                    foreach ($stockA_datas as $key => $v) {
                        $stockA_day_change = $stockA_datas[$key]['day_change'];
                        $stockB_day_change = $stockB_datas[$key]['day_change'];
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
                    $up = round(round($a / ($a + $b), 2) * 100);
                    if ($diff > 0 && $up > $out_up) {
                        $out_up = $up;
                        $up_diff = $diff;
                        $out_up_stockA_datas = $stockA_datas;
                        $out_up_stockB_datas = $stockB_datas;
                    }
                    $up_list->push($up);
                    //A跌B x天後 也跟著跌
                    $down = round(round($d / ($c + $d), 2) * 100);
                    if ($diff > 0 && $down > $out_down) {
                        $out_down = $down;
                        $down_diff = $diff;
                        $out_down_stockA_datas = $stockA_datas;
                        $out_down_stockB_datas = $stockB_datas;
                    }
                    $down_list->push($down);
                }
            }
        }

        return [
            $out_up, $up_diff, $up_list, $out_up_stockA_datas, $out_up_stockB_datas,
            $out_down, $down_diff, $down_list, $out_down_stockA_datas, $out_down_stockB_datas
        ];
    }






    public function cal_stock($data)
    {
        $startdate = $data->startdate; //'2021-01-01';
        $enddate = $data->enddate; //'2021-12-01';
        $diff = $data->diff;
        $upordown = $data->upordown;

        $stockA_id = $data->stockA_id;
        $stockB_id = $data->stockB_id;
        $stockA_name = StockName::get_stock_name($stockA_id);
        $stockB_name = StockName::get_stock_name($stockB_id);

        list($up, $down, $stockA_datas, $stockB_datas) = self::cal_two_stock($startdate, $enddate, $diff, $stockA_id, $stockB_id);

        if ($upordown == 1) {
            $sendresult = $stockA_name . "(" . $stockA_id . "黃線)" . "漲，" . $stockB_name . "(" . $stockB_id . "藍線)" . $diff . "天後 也跟著漲" . $up . "%";
        } else if ($upordown == 2) {
            $sendresult = $stockA_name . "(" . $stockA_id . "黃線)" . "跌，" . $stockB_name . "(" . $stockB_id . "藍線)" . $diff . "天後 也跟著跌" . $down . "%";
        } else {
            $sendresult = $stockA_name . "(" . $stockA_id . "黃線)" . "漲，" . $stockB_name . "(" . $stockB_id . "藍線)" . $diff . "天後 也跟著漲" . $up . "%    ,     " . $stockA_name . "(" . $stockA_id . "黃線)" . "跌，" . $stockB_name . "(" . $stockB_id . "藍線)" . $diff . "天後 也跟著跌" . $down . "%";
        }

        // $real_diff = (strtotime($stockB_datas[0]['date']) - strtotime($stockA_datas[0]['date'])) / (60 * 60 * 24);
        return response()->json(['success' => $sendresult, 'stockA_datas' => $stockA_datas, 'stockB_datas' => $stockB_datas], 200);
    }
    public function cal_two_stock($startdate, $enddate, $diff, $stockA, $stockB)
    {

        $stockA_datas = StockName::where(['stock_id' => $stockA])->first()->StockData->where('date', '>=', $startdate)->where('date', '<=', $enddate)->values();
        if (StockName::where(['stock_id' => $stockB])->first()->StockData->where('date', $stockA_datas[0]['date'])->count() != 0) {
            $stockB_datas = StockName::where(['stock_id' => $stockB])->first()->StockData->where('date', '>=', $startdate)->collect()->skip($diff)->take($stockA_datas->count())->values();
            if ($stockA_datas->count() != 0 && $stockB_datas->count() != 0 && ($stockA_datas->count() == $stockB_datas->count())) {
                $a = 0;
                $b = 0;
                $c = 0;
                $d = 0;
                foreach ($stockA_datas as $key => $v) {
                    $stockA_day_change = $stockA_datas[$key]['day_change'];
                    $stockB_day_change = $stockB_datas[$key]['day_change'];
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
                return [$up, $down, $stockA_datas, $stockB_datas];
            }
        }
    }
}
