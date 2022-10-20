<?php

namespace App\Repositories;

use App\Models\Bulletin;
use App\Models\StockCalculate;
use App\Models\StockCategory;
use App\Models\StockData;
use App\Models\StockName;
use App\Models\StockSpecialKindDetail;

use App\Jobs\CalculateStockJob;

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
}
