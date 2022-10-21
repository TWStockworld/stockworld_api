<?php

namespace App\Http\Controllers;


use App\Models\StockName;
use App\Models\StockCalculate;
use App\Models\StockCalculateGroup;
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
    public function test2()
    {
        $aa = collect();
        $aa->push(['up' => 1]);
        $aa->push(['up' => 2]);

        $aa = $aa->map(function ($item) {
            $item['sort'] = 1;
            return $item;
        });
        return response()->json(['success' => $aa], 200);
    }
    public function test3()
    {
        $bb = collect([1, 2, 3, 4, 5, 6, 7]);
        $aa = $bb->take(3);
        return response()->json(['success' =>  $aa . '22' . $bb], 200);
    }
    public function test4()
    {
        return response()->json(['success' => ''], 200);
    }
}
