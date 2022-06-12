<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;


use App\Models\StockCategory;
use App\Models\StockData;
use App\Models\StockName;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateStockRepository
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
                if (!StockCategory::where('category', $data['industry_category'])->exists()) {
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

    public function update_stock_data()
    {
        $stocks_chunked = StockCategory::where('category', "航運業")->first()->StockName->chunk(8);
        $start = '2020-01-01';
        $end = '2021-12-31';
        $insert_data = [];
        foreach ($stocks_chunked as $stocks_chunk) {
            $stock_request = fn (Pool $pool) => $stocks_chunk->map(
                fn (object $stock) => $pool->get(
                    'https://api.finmindtrade.com/api/v4/data',
                    ['dataset' => 'TaiwanStockPrice', 'data_id' => $stock->stock_id, 'start_date' => $start, 'end_date' => $end, 'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJkYXRlIjoiMjAyMi0wNS0wNSAxNzo0NzoxNiIsInVzZXJfaWQiOiJsb2dpdGVjaDA4NTciLCJpcCI6IjEwNi4xMDUuMTE2LjEwOCJ9.HmPjJB9uyUfDnekJeGcvvATmVIMf_jDhhcj4IZgJqlU']
                )
            );

            $responses = Http::pool($stock_request);

            foreach ($responses as $response) {
                $datas = $response->collect('data');
                if ($datas->count() != 0) {
                    $stock_virtual_id = StockName::get_virtual_stock_id($datas[0]['stock_id']);
                    $nowdata = StockData::where('stock_name_id', $stock_virtual_id)->get('date');
                    foreach ($datas as $data) {
                        if (
                            $data['close'] != 0.0
                            && $nowdata->where('date', $data['date'])->count() == 0
                            // && !StockData::check_stock_data_ifexists($stock_virtual_id, $data['date'])
                        ) {
                            $day_change = round(($data['spread'] / ($data['close'] - $data['spread'])) * 100, 2);
                            $stock_data = [
                                'date' => $data['date'], 'stock_name_id' => $stock_virtual_id, 'open' => $data['open'], 
                                'up' => $data['max'], 'down' => $data['min'],
                                'close' => $data['close'], 'day_change' => $day_change,
                                'created_at' => now()->toDateString(), 'updated_at' => now()->toDateString()
                            ];
                            $insert_data[] = $stock_data;
                        }
                    }
                }
            }
        }
        $chunks = array_chunk($insert_data, 500);

        foreach ($chunks as $chunk) {
            StockData::insert($chunk);
        }

        return response()->json(['success' => 'f'], 200);

        //另一種方法
        // $stocks = [
        //     'https://api.finmindtrade.com/api/v4/data?dataset=TaiwanStockPrice&data_id=0050&start_date=2021-01-01&end_date=2021-12-31',
        //     'https://api.finmindtrade.com/api/v4/data?dataset=TaiwanStockPrice&data_id=0051&start_date=2021-01-01&end_date=2021-12-31',
        // ];
        // $callback = function (Pool $pool) use ($stocks) {
        //     $requests = [];
        //     foreach ($stocks as $stock) {
        //         $requests[] = $pool->get($stock);
        //     }
        //     return $requests;
        // };
        // $responses = Http::pool($callback);
        //另一種方法
    }
}
