<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;

use App\Models\StockCategory;
use App\Models\StockName;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

use App\Jobs\UpdateStockDataJob;
use App\Jobs\UpdateStockDataFindmindJob;
use App\Jobs\UpdateStockInformationJob;
use App\Models\StockData;
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
        $data = StockData::where('date', '>=', '2019-01-01')->where('date', '<=', '2022-10-01')->take(1000)->get()->groupby('stock_name_id');
        $a = $data->get(99999);
        $c = 0;
        if ($a != null) {
            $c = 1;
        }
        return response()->json(['success' => $c], 200);
    }
    public function test1()
    {

        $as = collect([2, 4, 5, 8]);
        $bs = collect([1, 3, 5, 7]);
        $out = collect();
        foreach ($as as $a) {
            foreach ($bs as $b) {
                for ($c_diff = 1; $c_diff <= 5; $c_diff++) {
                    if (intval($a) % 2 != 0) {
                        break 2;
                    }
                    $out->push($a . $b);
                }
            }
        }
        return response()->json(['success' => $out], 200);
    }


    public function update_stock_information()
    {
        UpdateStockInformationJob::dispatch();
        return response()->json(['success' => '已自動開始更新，請稍等'], 200);
    }

    public function update_stock_data_findmind()
    {
        // UpdateStockDataFindmindJob::dispatch();
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
                UpdateStockDataJob::dispatch($input);
                $msg = "已自動開始更新 日期: " . $input . " 股票資料,請稍後";
            }
            return response()->json(['success' => $msg], 200);
        }
    }
}
