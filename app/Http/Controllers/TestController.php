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
        $stock_category_id = 25;
        $data = StockName::where('stock_category_id', $stock_category_id)->get();
        $B_category = collect();
        $BB_category = collect();
        $data->map(function ($item) use ($B_category, $stock_category_id) {
            // if ($item->StockCalculateStockA->StockAName->stock_category_id != $stock_category_id)
            $B_category->push($item->StockCalculateStockB);
        });
        $B_category->flatten(1)->map(function ($item) use ($BB_category, $stock_category_id) {
            if ($item->StockAName->stock_category_id != $stock_category_id) {
                $BB_category->push($item);
            }
        });

        return response()->json(['success' => $BB_category], 200);
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
        $stock_category_id = 5;

        if ($stock_category_id) {
            $B_category = collect();

            $data = StockName::where('stock_category_id', $stock_category_id)->get();
            $data->map(function ($item) use ($B_category) {
                $B_category->push($item->StockCalculateStockB);
            });
        }
        return response()->json(['A_B_same_category_up' => $B_category->flatten(1)], 200);
    }
    public function test4()
    {
        return response()->json(['success' => ''], 200);
    }
}
