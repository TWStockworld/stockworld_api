<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;

use App\Models\StockCategory;
use App\Models\StockData;
use App\Models\StockName;

class UpdateStockDataFindmindJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries = 2;

    public function __construct()
    {
        //
    }
    public function handle()
    {
        // $stocks_chunked = StockName::all();
        // $stocks_chunked = StockName::take(1550)->get();
        $stocks_chunked = StockName::skip(1550)->take(PHP_INT_MAX)->get();

        $stocks_chunked = $stocks_chunked->chunk(7);
        // $stocks_chunked = StockCategory::where('category', "電子零組件業")->first()->StockName->chunk(7);
        $start = '2000-01-01';
        $end = '2022-10-07';
        foreach ($stocks_chunked as $stocks_chunk) {
            $stock_request = fn (Pool $pool) => $stocks_chunk->map(
                fn (object $stock) => $pool->get(
                    'https://api.finmindtrade.com/api/v4/data',
                    ['dataset' => 'TaiwanStockPrice', 'data_id' => $stock->stock_id, 'start_date' => $start, 'end_date' => $end, 'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJkYXRlIjoiMjAyMi0wNS0wNSAxNzo0NzoxNiIsInVzZXJfaWQiOiJsb2dpdGVjaDA4NTciLCJpcCI6IjEwNi4xMDUuMTE2LjEwOCJ9.HmPjJB9uyUfDnekJeGcvvATmVIMf_jDhhcj4IZgJqlU']
                )
            );

            $responses = Http::pool($stock_request);

            foreach ($responses as $response) {
                $insert_data = collect();

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
                                'close' => $data['close'], 'day_change' => $day_change, 'volume' => $data['Trading_Volume'],
                                'money' => $data['Trading_money'], 'turnover' => $data['Trading_turnover'],
                                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')
                            ];
                            $insert_data->push($stock_data);
                        }
                    }
                }
                $allstock = $insert_data->toArray();
                $chunks = array_chunk($allstock, 500);
                foreach ($chunks as $chunk) {
                    StockData::insert($chunk);
                }
            }
        }
    }
}
