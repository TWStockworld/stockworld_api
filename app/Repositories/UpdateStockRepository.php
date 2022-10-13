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
        $out_stock_col = collect();

        self::test1($out_stock_col);
        $out_stock_col = $out_stock_col->sortByDesc('up')->values();
        return response()->json(['success' => $out_stock_col[0]], 200);
    }
    public function test1($out_stock_col)
    {
        $result = [
            'group_id' => 1, 'stockA_name_id' => 1, 'stockB_name_id' => 1, 'diff' => 1,
            'up' => 30, 'down' => 30, 'startdate' => 1, 'enddate' => 1,
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')
        ];
        $out_stock_col->push($result);
        $result = [
            'group_id' => 1, 'stockA_name_id' => 1, 'stockB_name_id' => 1, 'diff' => 1,
            'up' => 40, 'down' => 20, 'startdate' => 1, 'enddate' => 1,
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')
        ];
        $out_stock_col->push($result);
        $result = [
            'group_id' => 1, 'stockA_name_id' => 1, 'stockB_name_id' => 1, 'diff' => 1,
            'up' => 50, 'down' => 40, 'startdate' => 1, 'enddate' => 1,
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')
        ];
        $out_stock_col->push($result);
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
