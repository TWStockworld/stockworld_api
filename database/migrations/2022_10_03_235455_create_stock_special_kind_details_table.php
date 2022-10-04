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
        Schema::create('stock_special_kind_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bulletin_id');
            $table->foreign('bulletin_id')->references('id')->on('bulletins')->onDelete('cascade')->comment('外鍵_公告標題ID');
            $table->unsignedBigInteger('stock_special_kind_id');
            $table->foreign('stock_special_kind_id')->references('id')->on('stock_special_kinds')->onDelete('cascade')->comment('外鍵_特別種類標題ID');
            $table->unsignedBigInteger('stock_name_id');
            $table->foreign('stock_name_id')->references('id')->on('stock_names')->onDelete('cascade')->comment('外鍵_股票虛擬ID');
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
        Schema::dropIfExists('stock_special_kind_details');
    }
};
