<?php

namespace App\Repositories;

use App\Models\Bulletin;
use App\Models\StockCalculate;
use App\Models\StockCategory;
use App\Models\StockData;
use App\Models\StockName;
use App\Models\StockSpecialKindDetail;


class GetStockRepository
{

    public function get_stock_category()
    {
        return response()->json(['success' => StockCategory::all()], 200);
    }
    public function get_stock_name($request)
    {
        $stock_category_id = $request->stock_category_id;
        $stock_type = $request->stock_type;
        if ($stock_category_id == 0) {
            $stocks = StockName::all();
        } else {
            if ($stock_type) {
                $stocks = StockCategory::find($request->stock_category_id)->StockName->where('type', $stock_type)->values();
            } else {
                $stocks = StockCategory::find($request->stock_category_id)->StockName;
            }
        }

        return response()->json(['count' => $stocks->count(), 'success' => $stocks,], 200);
    }


    public function get_stock($request)
    {
        $stock_id = $request->stock_id;
        $stocks = StockName::where('stock_id', $stock_id)->first();
        $stock_data = $stocks->StockData;
        $stock_category = $stocks->StockCategory;
        $last_data = $stock_data->last();
        return response()->json([
            'count' => $stocks->count(), 'stock_name' => $stocks['stock_name'], 'stock_id' => $stock_id, 'stock_category_id' => $stock_category['id'],
            'stock_category' => $stock_category['category'],
            'stock_data' => $stock_data, 'last_data' => $last_data
        ], 200);
    }

    public function get_category_last_stock($request)
    {
        $page = $request->page;
        $stock_id = $request->stock_id;
        $request->stock_category_id;
        if ($request->stock_category_id != '') {
            $stocks = StockCategory::find($request->stock_category_id)->StockName->slice($page * 10, $page + 10)->values();
        } else {
            $stocks = StockName::where('stock_id', $stock_id)->first()->StockCategory->StockName->slice($page * 10, $page + 10)->values();
        }
        $stock_data = $stocks->map(function ($item) {
            return $item->StockData->last();
        });
        $stock_data = $stock_data->map(function ($item) {
            unset($item['id']);
            $item['stock_name'] = StockName::get_stock_name_useid($item['stock_name_id']);
            $item['stock_id'] = StockName::get_stock_id($item['stock_name_id']);
            $item['day_change'] = round($item['day_change'], 2);
            unset($item['created_at']);
            unset($item['updated_at']);
            unset($item['stock_name_id']);
            return $item;
        });
        return response()->json([
            'stocks' => $stock_data
        ], 200);
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
}
