<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;

use App\Models\StockCategory;
use App\Models\StockData;
use App\Models\StockName;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

use App\Jobs\UpdateStockData;
use App\Jobs\UpdateStockDataFindmind;

use App\Models\StockUpdateRecord;


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
        $input = "2022-09-12";
        $republicdate = date_create($input);
        $republicdate = $republicdate->modify("-1911 year");
        $dete0 = ltrim($republicdate->format("Y/m/d"), "0");

        $date = date_create($input);
        $date1 = date_format($date, "Ymd"); //上市

        $date = date_format($date, "Y-m-d"); //存資料庫
        // $date0 = "111/09/12"; //上櫃
        // $date1 = "20220912"; //上市
        return response()->json(['success' =>  $date . "   ddddddd   " . $dete0 . "   ddddddd   " . $date1], 200);
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
                'type' => 2, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')
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
                'type' => 3, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')
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

    public function update_stock_data_findmind()
    {
        // UpdateStockDataFindmind::dispatch();
        //id:4599147 1778

        return response()->json(['success' => '已自動開始更新，請稍等'], 200);
    }

    public function update_stock_data($request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
        ], [
            'required' => '請代入日期',
            'date.date' => '日期格式錯誤',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 401);
        } else {
            $input = $request->date;
            $msg = "日期: " . $input . " 股票資料已更新過";
            if (!StockUpdateRecord::where('date', $input)->first()) { //如果沒記錄到今天data 就進入
                UpdateStockData::dispatch($input);
                $msg = "已自動開始更新 日期: " . $input . " 股票資料,請稍後";
            }


            return response()->json(['success' => $msg], 200);
        }
    }
}
