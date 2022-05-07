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
        Schema::create('stock_data', function (Blueprint $table) {
            $table->id();
            $table->date('date')->comment('股票資料時間');
            $table->unsignedBigInteger('stock_name_id');
            $table->foreign('stock_name_id')->references('id')->on('stock_names')->onDelete('cascade')->comment('外鍵_股票虛擬ID');
            $table->float('close',8,2)->comment('股票收盤價');
            $table->float('day_change',8,2)->nullable()->comment('漲跌%數');
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
        Schema::dropIfExists('stock_data');
    }
};
