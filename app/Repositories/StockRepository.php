<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;


use App\Models\StockCategory;
use App\Models\StockData;
use App\Models\StockName;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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

    public function update_stock_data()
    {
        $stocks_chunked = StockCategory::where('category', "航運業")->first()->StockName->take(1)->chunk(8);
        $start = '2021-01-01';
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
                    foreach ($datas as $data) {
                        if (
                            $data['close'] != 0.0
                            &&!StockData::where(['stock_name_id' => $stock_virtual_id, 'date' => $data['date']])->exists()
                            // &&!DB::table('stock_data')->where(['stock_name_id' => $stock_virtual_id, 'date' => $data['date']])->exists()
                            // && !StockData::check_stock_data_ifexists($stock_virtual_id, $data['date'])
                        ) {
                            $day_change = round(($data['spread'] / ($data['close'] - $data['spread'])) * 100, 2);
                            $stock_data = [
                                'date' => $data['date'], 'stock_name_id' => $stock_virtual_id,
                                'close' => $data['close'], 'day_change' => $day_change,
                                'created_at' => now()->toDateString(), 'updated_at' => now()->toDateString()
                            ];
                            $insert_data[] = $stock_data;
                        }
                    }
                }
            }
        }
        $insert_data = collect($insert_data);
        $chunks = $insert_data->chunk(300);
        foreach ($chunks as $chunk) {
            StockData::insert($chunk->toArray());
        }
        // $a=StockData::where(['stock_name_id' => '597', 'date' => '2021-12-28'])->cursor()->count();
        // $unit=array('b','kb','mb','gb','tb','pb');
        // $mem=@round(memory_get_usage(true)/pow(1024,($i=floor(log(memory_get_usage(true),1024)))),2).' '.$unit[$i];
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

    public function cal_stock()
    {
        $startdate = '2021-01-01';
        $enddate = '2021-12-01';
        $diff = 1;

        $another_date = date('Y-m-d', strtotime($startdate . "+" . $diff . "days"));
        $Astock = StockData::where(['stock_name_id' => '814'])->where('date', '>=', $startdate)->where('date', '<=', $enddate)->first()->get('day_change');
        $Bstock = StockData::where(['stock_name_id' => '815'])->where('date', '>=', $another_date)->first()->get('day_change')->take($Astock->count());
        $a = 0;
        $b = 0;
        $c = 0;
        $d = 0;
        $days = $Astock->count();

        for ($i = 0; $i < $days; $i++) {
            if ($Astock[$i]['day_change'] > 0 && $Bstock[$i]['day_change'] > 0) {
                $a++;
            } else if ($Astock[$i]['day_change'] > 0 && $Bstock[$i]['day_change'] <= 0) {
                $b++;
            } else if ($Astock[$i]['day_change'] <= 0 && $Bstock[$i]['day_change'] > 0) {
                $c++;
            } else if ($Astock[$i]['day_change'] <= 0 && $Bstock[$i]['day_change'] <= 0) {
                $d++;
            }
        }
        //A漲 B x天後 也跟著漲
        $up = round($a / ($a + $b), 2);
        //A跌B x天後 也跟著跌
        $down = round($d / ($c + $d), 2);
        return response()->json(['success' =>  "A漲 B" . $diff . "天後 也跟著漲" . $up . "    ,     A跌 B" . $diff . "天後 也跟著跌" . $down], 200);

        // return response()->json(['success' =>  $another_date], 200);
    }
}
