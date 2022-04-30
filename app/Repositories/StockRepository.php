<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

use App\Models\StockCategory;
use App\Models\StockName;
use App\Models\User;


class StockRepository
{
    protected $user;

    public function __construct(User $user, StockName $stockName, StockCategory $stockCategory)
    {
        $this->user = $user;
        $this->stockName = $stockName;
        $this->stockCategory = $stockCategory;
    }

    public function update_stock_category()
    {
        $response = Http::get('https://api.finmindtrade.com/api/v4/data?dataset=TaiwanStockInfo');
        $cate = [];
        $datas = $response->collect()->get('data');
        foreach ($datas as $data) {
            if (!in_array($data['industry_category'], $cate)) {
                array_push($cate, $data['industry_category']);
                if (!StockCategory::where('category', '=', $data['industry_category'])->exists()) {
                    StockCategory::create(['category' => $data['industry_category']]);
                }
            }
        }
        return response()->json(['stockjson' => $cate], 200);
    }
    public function update_stock_name()
    {
        $response = Http::get('https://api.finmindtrade.com/api/v4/data?dataset=TaiwanStockInfo');
        $datas = $response->collect()->get('data');
        foreach ($datas as $data) {
            $stock_category_id = StockCategory::get_stock_category_id($data['industry_category']);
            if (StockName::where('stock_id', '=', $data['stock_id'])->exists()) {
                if ($data['industry_category'] != "其他") {
                    StockName::where('stock_id', '=', $data['industry_category'])->update([
                        'stock_category_id' => $stock_category_id,
                        'stock_id' => $data['stock_id'], 'stock_name' => $data['stock_name']
                    ]);
                }
            } else {
                StockName::create([
                    'stock_category_id' => $stock_category_id,
                    'stock_id' => $data['stock_id'], 'stock_name' => $data['stock_name']
                ]);
            }
        }

        return response()->json(['success' => "success"], 200);
    }

}
