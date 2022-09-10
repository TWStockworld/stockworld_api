<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;

use App\Models\StockCategory;
use App\Models\StockData;
use App\Models\StockName;
use App\Models\User;

use App\Jobs\UpdateStockData;

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
            //上櫃股票基本資料OTCs
            $pool->get('https://www.tpex.org.tw/openapi/v1/mopsfin_t187ap03_O'),

            //上市公司基本資料SEMs
            $pool->get('https://openapi.twse.com.tw/v1/opendata/t187ap03_L'),

            //股票分類(制式 standard)
            $pool->get('https://bakerychu.com/file/Industry_category.json'),
        ]);

        //新增種類
        $cate = collect();

        $OTCs = $responses[0]->collect();
        $SEMs = $responses[1]->collect();
        $standard_categorys = $responses[2]->collect();

        foreach ($standard_categorys as $standard_category) {
            if (!$cate->contains($standard_category['category'])) {
                $cate->push($standard_category['category']);
                if (!StockCategory::where('category', $standard_category['category'])->exists()) {
                    StockCategory::create(['category' => $standard_category['category']]);
                }
            }
        }

        $allstock = collect();
        foreach ($OTCs as $OTC) {
            if ($standard_categorys->contains('SecuritiesIndustryCode', $OTC['SecuritiesIndustryCode'])) {
                $stock_category_id = StockCategory::get_stock_category_id($standard_categorys->where('SecuritiesIndustryCode', $OTC['SecuritiesIndustryCode'])->first()['category']);
            } else {
                $stock_category_id = StockCategory::get_stock_category_id('其他');
            }
            $newdata =  [
                'stock_category_id' => $stock_category_id,
                'stock_id' => $OTC['SecuritiesCompanyCode'],
                'stock_name' => $OTC['公司簡稱'],
                'type' => 2, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")
            ];
            $allstock->push($newdata);
        }
        foreach ($SEMs as $SEM) {
            if ($standard_categorys->contains('SecuritiesIndustryCode', $SEM['產業別'])) {
                $stock_category_id = StockCategory::get_stock_category_id($standard_categorys->where('SecuritiesIndustryCode', $SEM['產業別'])->first()['category']);
            } else {
                $stock_category_id = StockCategory::get_stock_category_id('其他');
            }
            $newdata =  [
                'stock_category_id' => $stock_category_id,
                'stock_id' => $SEM['公司代號'],
                'stock_name' => $SEM['公司簡稱'],
                'type' => 3, 'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")
            ];
            $allstock->push($newdata);
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

        UpdateStockData::dispatch();
        
        return response()->json(['success' => '已自動開始更新，請稍等'], 200);
    }
}
