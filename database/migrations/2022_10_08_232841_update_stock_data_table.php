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
        Schema::table('stock_data', function (Blueprint $table) {
            $table->unsignedBigInteger('turnover')->comment('成交筆數')->after('day_change');
            $table->unsignedBigInteger('money')->comment('成交金額')->after('day_change');
            $table->unsignedBigInteger('volume')->comment('交易量')->after('day_change');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_data', function (Blueprint $table) {
            $table->dropColumn('turnover');
            $table->dropColumn('money');
            $table->dropColumn('volume');
        });
    }
};
