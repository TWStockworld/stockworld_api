<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StockController;

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

php artisan route:list 查看可用
php artisan l5-swagger:generate
*/


Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::prefix('stock')->group(function () {

    Route::get('test', [StockController::class, 'test']);
    Route::get('update_stock_information', [StockController::class, 'update_stock_information']);
    Route::get('update_stock_data', [StockController::class, 'update_stock_data']);

    Route::get('get_stock_category', [StockController::class, 'get_stock_category']);
    Route::get('get_stock_name', [StockController::class, 'get_stock_name']);
    Route::get('get_stock_count', [StockController::class, 'get_stock_count']);
    Route::get('get_stock', [StockController::class, 'get_stock']);

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

https://www.tpex.org.tw/web/stock/aftertrading/daily_trading_info/st43_result.php?l=zh-tw&d=111/09&stkno=3297
*/