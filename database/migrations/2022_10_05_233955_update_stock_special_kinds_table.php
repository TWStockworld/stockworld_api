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
        Schema::table('stock_special_kinds', function (Blueprint $table) {
            $table->unsignedBigInteger('bulletin_id')->after('title')->nullable();;
            $table->foreign('bulletin_id')->references('id')->on('bulletins')->onDelete('cascade')->comment('外鍵_公告標題ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_special_kinds', function (Blueprint $table) {
            $table->dropColumn('bulletin_id');
        });
    }
};