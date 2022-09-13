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
        Schema::create('stock_update_records', function (Blueprint $table) {
            $table->id();
            $table->date('date')->comment('股票日期');
            $table->unsignedBigInteger('status_otc')->comment('上櫃股票更新狀態 0:未更新 1:已更新')->default(0);
            $table->unsignedBigInteger('status_sem')->comment('上市股票更新狀態 0:未更新 1:已更新')->default(0);
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
        Schema::dropIfExists('stock_update_records');
    }
};
