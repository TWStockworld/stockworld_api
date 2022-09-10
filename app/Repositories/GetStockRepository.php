<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;


use App\Models\StockCategory;
use App\Models\StockData;
use App\Models\StockName;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GetStockRepository
{
    protected $user;

    // public function __construct(User $user, StockName $stockName, StockCategory $stockCategory,StockData $stockData)
    // {
    //     $this->user = $user;
    //     $this->stockName = $stockName;
    //     $this->stockCategory = $stockCategory;
    //     $this->stockData = $stockData;
    // }


    public function get_stock_category()
    {
        return response()->json(['success' => StockCategory::all()], 200);
    }
    public function get_stock_name()
    {
        $stocks = StockCategory::where('category', "電子零組件業")->first()->StockName;
    
        // $stocks = StockName::where(['stock_category_id' => 14])->take(80)->get();
        return response()->json(['count' => $stocks->count(), 'time' => date("Y-m-d H:i:s"),'success' => $stocks,], 200);
    }
    public function get_stock_count()
    {
        $stocks = StockData::select('stock_name_id')->distinct()->get();
        return response()->json(['count' => $stocks->count(), 'success' => $stocks], 200);
    }

    public function get_stock()
    {
        $stocks = StockData::where('stock_name_id',1654)->get();
        return response()->json(['count' => $stocks->count(), 'success' => $stocks], 200);
    }

    public function cal_stock($data)
    {
        $startdate = $data->startdate; //'2021-01-01';
        $enddate = $data->enddate; //'2021-12-01';
        $diff = $data->diff;
        $category = $data->category;

        if ($category != null) {
            $stocks = StockName::where(['stock_category_id' => $category])->get();
            $stock_list = [];
            foreach ($stocks as $stockA) {
                foreach ($stocks as $stockB) {
                    $stockA_id = $stockA->stock_id;
                    $stockB_id = $stockB->stock_id;

                    if ($stockA_id != $stockB_id) {
                        list($up, $down, $stockA_datas, $stockB_datas) = self::cal_two_stock($startdate, $enddate, $diff, $stockA_id, $stockB_id);
                        $result = ['up' => $up, 'down' => $down, 'stockA_datas' => $stockA_datas, 'stockB_datas' => $stockB_datas];
                        array_push($stock_list, $result);
                        // break;
                    }
                }
            }
            usort($stock_list, function ($a, $b) {
                return $a['up'] <=> $b['up'];
            });
        } else {
            $stockA = $data->stockA;
            $stockB = $data->stockB;
            $stockA_name = StockName::get_stock_name($stockA);
            $stockB_name = StockName::get_stock_name($stockB);

            list($up, $down, $stockA_datas, $stockB_datas) = self::cal_two_stock($startdate, $enddate, $diff, $stockA, $stockB);

            $sendresult = $stockA_name . "(" . $stockA . ")" . "漲，" . $stockB_name . "(" . $stockB . ")" . $diff . "天後 也跟著漲" . $up . "%    ,     " . $stockA_name . "(" . $stockA . ")" . "跌，" . $stockB_name . "(" . $stockB . ")" . $diff . "天後 也跟著跌" . $down . "%";

            return response()->json(['success' => $sendresult, 'stockA_datas' => $stockA_datas, 'stockB_datas' => $stockB_datas], 200);
        }

        return response()->json(['success' => $stock_list], 200);
    }


    public function cal_two_stock($startdate, $enddate, $diff, $stockA, $stockB)
    {

        $stockA_datas = StockName::where(['stock_id' => $stockA])->first()->StockData->where('date', '>=', $startdate)->where('date', '<=', $enddate)->values();
        $stockB_datas = StockName::where(['stock_id' => $stockB])->first()->StockData->where('date', '>=', $startdate)->collect()->skip($diff)->take($stockA_datas->count())->values();
        if ($stockA_datas->count() != 0 && $stockB_datas->count() != 0) {

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
            return [$up, $down, $stockA_datas, $stockB_datas];
        }
    }
}
