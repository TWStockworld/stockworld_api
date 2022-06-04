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
            $table->float('down',8,2)->comment('股票最低價')->after('stock_name_id');
            $table->float('up',8,2)->comment('股票最高價')->after('stock_name_id');
            $table->float('open',8,2)->comment('股票開盤價')->after('stock_name_id');
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
            $table->dropColumn('down');
            $table->dropColumn('up');
            $table->dropColumn('open');
        });
    }
};
