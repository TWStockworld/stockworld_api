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
        Schema::table('stock_names', function (Blueprint $table) {
            $table->unsignedBigInteger('type')->after('stock_name')->comment('1:興櫃,2:上櫃,3:上市,4:其他');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_names', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
