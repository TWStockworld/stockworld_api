<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_calculates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_calculate_group_id');
            $table->foreign('stock_calculate_group_id')->references('id')->on('stock_calculate_groups')->onDelete('cascade')->comment('外鍵_日期群組ID');
            $table->unsignedBigInteger('stockA_name_id');
            $table->foreign('stockA_name_id')->references('id')->on('stock_names')->onDelete('cascade')->comment('外鍵_股票A虛擬ID');
            $table->unsignedBigInteger('stockB_name_id');
            $table->foreign('stockB_name_id')->references('id')->on('stock_names')->onDelete('cascade')->comment('外鍵_股票B虛擬ID');
            $table->unsignedInteger('diff')->comment('相差天數');
            $table->unsignedInteger('up')->comment('A漲 B x天後 也跟著漲 趴數');
            $table->unsignedInteger('down')->comment('A跌 B x天後 也跟著跌 趴數');
            $table->unsignedInteger('sort')->comment('1:up ,2:down');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_calculates');
    }
};
