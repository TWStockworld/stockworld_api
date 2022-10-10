<?php

namespace App\Repositories;

use App\Models\Bulletin;
use App\Models\StockCalculate;
use App\Models\StockCategory;
use App\Models\StockData;
use App\Models\StockName;
use App\Models\StockSpecialKindDetail;

use App\Jobs\CalculateStock;

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
    public function get_stock_name($request)
    {
        $stocks = StockCategory::find($request->stock_category_id)->StockName;
        return response()->json(['count' => $stocks->count(), 'time' => date('Y-m-d H:i:s'), 'success' => $stocks,], 200);
    }
    public function get_stock_count()
    {
        $stocks = StockData::select('stock_name_id')->distinct()->get();
        return response()->json(['count' => $stocks->count(), 'success' => $stocks], 200);
    }


    public function get_stock($request)
    {
        $stock_id = $request->stock_id;
        $stocks = StockName::where('stock_id', $stock_id)->first()->StockData;
        return response()->json(['count' => $stocks->count(), 'success' => $stocks], 200);
    }


    public function get_bulletin()
    {
        $bulletins = Bulletin::all();
        return response()->json(['success' => $bulletins], 200);
    }
    public function get_stock_special_kind($request)
    {
        $bulletin_id = $request->bulletin_id;
        $stock_special_kind = Bulletin::find($bulletin_id)->StockSpecialKind;
        return response()->json(['success' => $stock_special_kind], 200);
    }
    public function get_stock_special_kind_detail($request)
    {
        $bulletin_id = $request->bulletin_id;
        $stock_special_kind_id = $request->stock_special_kind_id;
        $stock_special_kind_detail = StockSpecialKindDetail::where(['bulletin_id' => $bulletin_id, 'stock_special_kind_id' => $stock_special_kind_id])->get();

        $stocks = collect();
        $stock_special_kind_detail->map(function ($item) use ($stocks) {
            $stocks->push($item->stockname);
        });
        return response()->json(['success' => $stocks], 200);
    }

    public function cal_stock($data)
    {
        $startdate = $data->startdate; //'2021-01-01';
        $enddate = $data->enddate; //'2021-12-01';
        $diff = $data->diff;
        $stock_category_id = $data->stock_category_id;

        if ($stock_category_id != null) {
            CalculateStock::dispatch($startdate, $enddate, $diff, $stock_category_id);

            return response()->json(['success' => '已自動開始計算，請稍等'], 200);
        } else {
            // $stockA = $data->stockA;
            // $stockB = $data->stockB;
            // $stockA_name = StockName::get_stock_name($stockA);
            // $stockB_name = StockName::get_stock_name($stockB);

            // list($up, $down) = self::cal_two_stock($startdate, $enddate, $diff, $stockA, $stockB);

            // $sendresult = $stockA_name . "(" . $stockA . ")" . "漲，" . $stockB_name . "(" . $stockB . ")" . $diff . "天後 也跟著漲" . $up . "%    ,     " . $stockA_name . "(" . $stockA . ")" . "跌，" . $stockB_name . "(" . $stockB . ")" . $diff . "天後 也跟著跌" . $down . "%";

            // return response()->json(['success' => $sendresult, 'stockA_datas' => $stockA_datas, 'stockB_datas' => $stockB_datas], 200);
        }

        // return response()->json(['success' => $stock_list], 200);
    }
    public function cal_two_stock($data)
    {

        $startdate = $data->startdate;
        $enddate = $data->enddate;
        $diff = $data->diff;
        $stockA = $data->stockA;
        $stockB = $data->stockB;

        $stockA_datas = StockName::where(['stock_id' => $stockA])->first()->StockData->where('date', '>=', $startdate)->where('date', '<=', $enddate)->values();
        if (StockName::where(['stock_id' => $stockB])->first()->StockData->where('date', $stockA_datas[0]['date'])->count() != 0) {
            $stockB_datas = StockName::where(['stock_id' => $stockB])->first()->StockData->where('date', '>=', $stockA_datas[0]['date'])->collect()->skip($diff)->take($stockA_datas->count())->values();
            if ($stockA_datas->count() != 0 && $stockB_datas->count() != 0 && ($stockA_datas->count() == $stockB_datas->count())) {
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
                return response()->json(['success' =>  $up . ' stop ' . $down], 200);
            } else {
                return response()->json(['no' => 'no'], 200);
            }
        } else {
            return response()->json(['no' => 'no'], 200);
        }
    }
}
