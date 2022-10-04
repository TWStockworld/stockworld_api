<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->resource('bulletins', BulletinController::class);
    $router->resource('stock_special_kinds', StockSpecialKindController::class);
    $router->resource('stock_special_kind_details', StockSpecialKindDetailController::class);
});
