<?php

namespace App\Http\Controllers;


use App\Models\StockName;
use App\Models\StockCalculate;
use App\Models\TestStock;
use App\Models\StockData;


class TestController extends Controller
{

    public function test1()
    {
        $startdate = '2018-01-01';
        $enddate = '2022-09-01';
        $diff = 10;
        $add_diff_enddate = date("Y-m-d", strtotime($enddate . '+ 15 days'));

        $stock_data_temp = StockData::where('date', '>=', $startdate)->where('date', '<=', $add_diff_enddate)->get()->groupby('stock_name_id');

        return response()->json(['success' => $stock_data_temp], 200);
    }
}
