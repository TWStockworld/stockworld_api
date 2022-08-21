<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Collection;

use App\Models\StockCategory;
use App\Models\StockData;
use App\Models\StockName;
use App\Models\User;

class UpdateStockRepository
{
    protected $user;

    public function __construct(User $user, StockName $stockName, StockCategory $stockCategory)
    {
        $this->user = $user;
        $this->stockName = $stockName;
        $this->stockCategory = $stockCategory;
    }
    public function test()
    {
        // $responses = Http::pool(fn (Pool $pool) => [
        //     //finmind台股
        //     $pool->get('https://api.finmindtrade.com/api/v4/data?dataset=TaiwanStockInfo'),

        //     //興櫃公司基本資料ESMs
        //     $pool->get('https://www.tpex.org.tw/openapi/v1/mopsfin_t187ap03_R'),

        //     //上櫃股票基本資料OTCs
        //     $pool->get('https://www.tpex.org.tw/openapi/v1/mopsfin_t187ap03_O'),

        //     //上市公司基本資料SEMs
        //     $pool->get('https://openapi.twse.com.tw/v1/opendata/t187ap03_L'),

        //     //股票分類(制式 standard)
        //     $pool->get('https://bakerychu.com/file/Industry_category.json'),
        // ]);
        $stocks_chunked = StockCategory::where('category', "航運業")->first()->StockName;

        return response()->json(['success' => $stocks_chunked], 200);
    }
    public function update_stock_information()
    {
        $responses = Http::pool(fn (Pool $pool) => [
            //finmind台股
            $pool->get('https://api.finmindtrade.com/api/v4/data?dataset=TaiwanStockInfo'),

            //興櫃公司基本資料ESMs
            $pool->get('https://www.tpex.org.tw/openapi/v1/mopsfin_t187ap03_R'),

            //上櫃股票基本資料OTCs
            $pool->get('https://www.tpex.org.tw/openapi/v1/mopsfin_t187ap03_O'),

            //上市公司基本資料SEMs
            $pool->get('https://openapi.twse.com.tw/v1/opendata/t187ap03_L'),

            //股票分類(制式 standard)
            $pool->get('https://bakerychu.com/file/Industry_category.json'),
        ]);

        //新增種類
        $cate = collect();
        $datas = $responses[0]->collect()->get('data');
        foreach ($datas as $data) {
            if (!$cate->contains($data['industry_category'])) {
                $cate->push($data['industry_category']);
                if (!StockCategory::where('category', $data['industry_category'])->exists()) {
                    StockCategory::create(['category' => $data['industry_category']]);
                }
            }
        }

        //股票分類(制式 standard)
        $standard_category = $responses[4]->collect();

        $ESMs = $responses[1]->collect();
        $OTCs = $responses[2]->collect();
        $SEMs = $responses[3]->collect();

        $allstock = collect();
        foreach ($ESMs as $ESM) {
            if ($standard_category->contains('SecuritiesIndustryCode', $ESM['SecuritiesIndustryCode'])) {
                $stock_category_id = StockCategory::get_stock_category_id($standard_category->where('SecuritiesIndustryCode', $ESM['SecuritiesIndustryCode'])->first()['category']);
            } else {
                $stock_category_id = StockCategory::get_stock_category_id('其他');
            }
            $newdata =  [
                'stock_category_id' => $stock_category_id,
                'stock_id' => $ESM['SecuritiesCompanyCode'],
                'stock_name' => $ESM['公司簡稱'],
                'type' => 1, 'created_at' => now()->toDateString(), 'updated_at' => now()->toDateString()
            ];
            $allstock->push($newdata);
        }
        foreach ($OTCs as $OTC) {
            if ($standard_category->contains('SecuritiesIndustryCode', $OTC['SecuritiesIndustryCode'])) {
                $stock_category_id = StockCategory::get_stock_category_id($standard_category->where('SecuritiesIndustryCode', $OTC['SecuritiesIndustryCode'])->first()['category']);
            } else {
                $stock_category_id = StockCategory::get_stock_category_id('其他');
            }
            $newdata =  [
                'stock_category_id' => $stock_category_id,
                'stock_id' => $OTC['SecuritiesCompanyCode'],
                'stock_name' => $OTC['公司簡稱'],
                'type' => 2, 'created_at' => now()->toDateString(), 'updated_at' => now()->toDateString()
            ];
            $allstock->push($newdata);
        }
        foreach ($SEMs as $SEM) {
            if ($standard_category->contains('SecuritiesIndustryCode', $SEM['產業別'])) {
                $stock_category_id = StockCategory::get_stock_category_id($standard_category->where('SecuritiesIndustryCode', $SEM['產業別'])->first()['category']);
            } else {
                $stock_category_id = StockCategory::get_stock_category_id('其他');
            }
            $newdata =  [
                'stock_category_id' => $stock_category_id,
                'stock_id' => $SEM['公司代號'],
                'stock_name' => $SEM['公司簡稱'],
                'type' => 3, 'created_at' => now()->toDateString(), 'updated_at' => now()->toDateString()
            ];
            $allstock->push($newdata);
        }
        $finminds = $responses[0]->collect()->get('data');
        foreach ($finminds as $finmind) {
            if (!$allstock->containsStrict('stock_id', $finmind['stock_id'])) {
                if ($finmind['type'] == 'tpex') {
                    $type = 2;
                } elseif ($finmind['type'] == 'twse') {
                    $type = 3;
                }
                $newdata =  [
                    'stock_category_id' => StockCategory::get_stock_category_id($finmind['industry_category']),
                    'stock_id' => $finmind['stock_id'],
                    'stock_name' => $finmind['stock_name'],
                    'type' => $type, 'created_at' => now()->toDateString(), 'updated_at' => now()->toDateString()
                ];
                $allstock->push($newdata);
            }
        }
        $allstock->sortBy('stock_id');

        $allstock->map(function ($item, $key) use ($allstock) {
            if (StockName::where('stock_id', $item['stock_id'])->exists()) {
                $allstock->forget($key);
            }
        });

        $allstock = $allstock->toArray();

        $chunks = array_chunk($allstock, 500);
        foreach ($chunks as $chunk) {
            StockName::insert($chunk);
        }

        return response()->json(['success' => $allstock], 200);
    }

    public function update_stock_data()
    {
        $stocks_chunked = StockCategory::where('category', "半導體業")->first()->StockName->chunk(8);
        $start = '2020-01-01';
        $end = '2021-12-31';
        $insert_data = collect();
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
                    $stock_name_id = StockName::get_stock_name_id($datas[0]['stock_id']);
                    foreach ($datas as $data) {
                        if (
                            $data['close'] != 0.0
                        ) {
                            $day_change = round(($data['spread'] / ($data['close'] - $data['spread'])) * 100, 2);
                            $stock_data = [
                                'date' => $data['date'], 'stock_name_id' => $stock_name_id, 'open' => $data['open'],
                                'up' => $data['max'], 'down' => $data['min'],
                                'close' => $data['close'], 'day_change' => $day_change,
                                'created_at' => now()->toDateString(), 'updated_at' => now()->toDateString()
                            ];
                            $insert_data->push($stock_data);
                        }
                    }
                }
            }
        }
        $insert_data->map(function ($item, $key) use ($insert_data) {
            if (StockData::where(['stock_name_id' => $item['stock_name_id'], 'date' => $item['date']])->exists()) {
                $insert_data->forget($key);
            }
        });

        $allstock = $insert_data->toArray();
        $chunks = array_chunk($allstock, 500);
        foreach ($chunks as $chunk) {
            StockData::insert($chunk);
        }

        return response()->json(['success' => 'f'], 200);
    }
}
