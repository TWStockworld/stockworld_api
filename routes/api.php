<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|

php artisan make:model Name --migration

php artisan make:migration update_flights_table

php artisan migrate
php artisan migrate:rollback

php artisan route:list 查看可用
php artisan queue:clear
sudo supervisorctl restart queue-worker:queue-worker_00

php artisan schedule:list 查看排程
php artisan l5-swagger:generate
    //     在linux crontab -e 
    //     添加 * * * * * /usr/bin/php /var/www/stockworld_api/artisan schedule:run >> /dev/null 2>&1

supervisor 設定
queue-worker.conf

[program:queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/stockworld_api/artisan queue:work --timeout=86400 --tries=2
autostart=true
autorestart=true
user=root
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/stockworld_api/storage/logs/supervisord.log

*/


Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::prefix('stock')->group(function () {
    Route::get('sendmail', [Controller::class, 'sendmail']);

    Route::get('test', [StockController::class, 'test']);
    Route::get('update_stock_information', [StockController::class, 'update_stock_information']);
    Route::get('update_stock_data_findmind', [StockController::class, 'update_stock_data_findmind']);
    Route::post('update_stock_data', [StockController::class, 'update_stock_data']);

    Route::get('get_stock_category', [StockController::class, 'get_stock_category']);
    Route::post('get_stock_name', [StockController::class, 'get_stock_name']);
    Route::get('get_stock_count', [StockController::class, 'get_stock_count']);
    Route::post('get_stock', [StockController::class, 'get_stock']);
    Route::get('get_bulletin', [StockController::class, 'get_bulletin']);
    Route::post('get_stock_special_kind', [StockController::class, 'get_stock_special_kind']);
    Route::post('get_stock_special_kind_detail', [StockController::class, 'get_stock_special_kind_detail']);

    Route::post('cal_stock_category', [StockController::class, 'cal_stock_category']);
    Route::post('cal_stock', [StockController::class, 'cal_stock']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'userInfo']);
});


/*
興櫃公司基本資料
https://www.tpex.org.tw/openapi/v1/mopsfin_t187ap03_R

上櫃股票基本資料
https://www.tpex.org.tw/openapi/v1/mopsfin_t187ap03_O

上市公司基本資料
https://openapi.twse.com.tw/v1/opendata/t187ap03_L

https://www.twse.com.tw/exchangeReport/STOCK_DAY?response=json&date=2022/09/08&stockNo=0050

https://www.twse.com.tw/exchangeReport/MI_INDEX?response=json&date=20220908&type=ALL


https://www.tpex.org.tw/web/stock/aftertrading/daily_trading_info/st43_result.php?l=zh-tw&d=111/09&stkno=3297

http://www.tpex.org.tw/web/stock/aftertrading/daily_close_quotes/stk_quote_result.php?d=111/09/08
*/