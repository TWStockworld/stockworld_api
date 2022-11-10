<?php

namespace App\Http\Controllers;


use App\Models\StockName;
use App\Models\StockCalculate;
use App\Models\StockCalculateGroup;
use App\Models\TestStock;
use App\Models\StockData;
use App\Models\StockSpecialKindDetail;

class TestController extends Controller
{

    public function test1()
    {
        $bulletin_id = 4;

        $stock_calculate_group_id = 1;

        if ($bulletin_id) {
            $A_B_same_bulletin = collect();
            $B_bulletin = collect();
            $BB_bulletin = collect();
            $data = StockSpecialKindDetail::where('bulletin_id', $bulletin_id)->get();
            $all_stock_id = collect();
            $data->map(function ($item, $key) use ($all_stock_id, $data) {
                if (!$all_stock_id->contains($item['stock_name_id'])) {
                    $all_stock_id->push($item['stock_name_id']);
                } else {
                    $data->forget($key);
                }
            });
            $data->map(function ($item) use ($B_bulletin) {
                $B_bulletin->push($item->StockName->StockCalculateStockB);
            });
            $B_bulletin->flatten(1)->map(function ($item) use ($BB_bulletin, $bulletin_id) {
                // if ($item->StockAName->stock_category_id != $bulletin_id) {
                $BB_bulletin->push($item->StockAName);
                // }
            });
        }
        return response()->json(['success' => $BB_bulletin], 200);
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
