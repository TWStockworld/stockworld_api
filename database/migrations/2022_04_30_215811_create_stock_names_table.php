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
        Schema::create('stock_names', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_category_id');
            $table->foreign('stock_category_id')->references('id')->on('stock_categories')->onDelete('cascade')->comment('股票類別ID');
            $table->string('stock_id')->comment('股票ID');
            $table->string('stock_name')->comment('股票中文名稱');

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
        Schema::dropIfExists('stock_names');
    }
};
