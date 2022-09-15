<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;

use App\Models\StockUpdateRecord;
use App\Models\StockData;
use App\Models\StockName;

class UpdateStockData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries = 2;
    protected $input; // you forgot of put this line

    public function __construct($input)
    {
        $this->input = $input;
        //
    }
    public function handle()
    {
        if (!StockUpdateRecord::where('date', $this->input)->first()) {
            $insert_data = collect();

            $republicdate = date_create($this->input);
            $republicdate = $republicdate->modify("-1911 year");
            $date0 = ltrim($republicdate->format("Y/m/d"), "0");

            $date = date_create($this->input);
            $date1 = date_format($date, "Ymd"); //上市

            $date = date_format($date, "Y-m-d"); //存資料庫
            // $date0 = "111/09/12"; //上櫃
            // $date1 = "20220912"; //上市
            $responses = Http::pool(fn (Pool $pool) => [
                //上櫃股票
                $pool->get('http://www.tpex.org.tw/web/stock/aftertrading/daily_close_quotes/stk_quote_result.php?', [
                    'd' => $date0
                ]),

                //上市股票
                $pool->get('https://www.twse.com.tw/exchangeReport/MI_INDEX', [
                    'type' => 'ALL', 'date' => $date1,
                ]),
            ]);

            //上櫃股票
            $newstocks = $responses[0]->collect('aaData');
            if ($newstocks->count() != 0) {

                $stocks = StockName::where('type', 2)->get();

                $stocks = $stocks->map(function ($stock) use ($date, $newstocks, $insert_data) {

                    $newstock = $newstocks->filter(function ($newstock) use ($stock) {
                        if ($newstock[0] == $stock->stock_id) {
                            return $newstock;
                        }
                    });
                    if ($newstock->count()) {
                        $close = $newstock->values()->pluck(16)->get(0);
                        if (
                            $close != "--"
                        ) {
                            $stock_name_id = StockName::get_stock_name_id($stock->stock_id);

                            $open = doubleval($newstock->values()->pluck(4)->get(0));
                            $up = doubleval($newstock->values()->pluck(5)->get(0));
                            $down = doubleval($newstock->values()->pluck(6)->get(0));
                            $close = doubleval($newstock->values()->pluck(16)->get(0));
                            $spread = doubleval($newstock->values()->pluck(3)->get(0));


                            $day_change = round(($spread / ($close - $spread)) * 100, 2);
                            $stock_data = [
                                'date' => $date, 'stock_name_id' => $stock_name_id, 'open' => $open,
                                'up' => $up, 'down' => $down,
                                'close' => $close, 'day_change' => $day_change,
                                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')
                            ];
                            $insert_data->push($stock_data);
                        }
                    }
                });
            }

            //上市股票
            $newstocks = $responses[1]->collect('data9');
            if ($newstocks->count() != 0) {

                $stocks = StockName::where('type', 3)->get();

                $stocks = $stocks->map(function ($stock) use ($date, $newstocks, $insert_data) {

                    $newstock = $newstocks->filter(function ($newstock) use ($stock) {
                        if ($newstock[0] == $stock->stock_id) {
                            return $newstock;
                        }
                    });
                    if ($newstock->count()) {
                        $close = $newstock->values()->pluck(8)->get(0);
                        if (
                            $close != "--"
                        ) {
                            $stock_name_id = StockName::get_stock_name_id($stock->stock_id);
                            $open = doubleval($newstock->values()->pluck(5)->get(0));
                            $up = doubleval($newstock->values()->pluck(6)->get(0));
                            $down = doubleval($newstock->values()->pluck(7)->get(0));
                            $close = doubleval($newstock->values()->pluck(8)->get(0));
                            $sign = strip_tags(strval($newstock->values()->pluck(9)->get(0)));
                            $value = $newstock->values()->pluck(10)->get(0);
                            $spread = doubleval($sign . $value);

                            $day_change = round(($spread / ($close - $spread)) * 100, 2);
                            $stock_data = [
                                'date' => $date, 'stock_name_id' => $stock_name_id, 'open' => $open,
                                'up' => $up, 'down' => $down,
                                'close' => $close, 'day_change' => $day_change,
                                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')
                            ];
                            $insert_data->push($stock_data);
                        }
                    }
                });
            }
            if ($insert_data->count()) {
                $allstock = $insert_data->toArray();
                $chunks = array_chunk($allstock, 500);
                foreach ($chunks as $chunk) {
                    StockData::insert($chunk);
                }
                StockUpdateRecord::create(['date' => $date, 'status_otc' => 1, 'status_sem' => 1]);
            }
        }
    }
}
