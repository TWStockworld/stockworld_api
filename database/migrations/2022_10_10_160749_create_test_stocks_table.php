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
        Schema::create('test_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('test1')->nullable();
            $table->string('test2')->nullable();
            $table->string('test3')->nullable();
            $table->string('test4')->nullable();
            $table->string('test5')->nullable();
            $table->string('test6')->nullable();
            $table->string('test7')->nullable();
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
        Schema::dropIfExists('test_stocks');
    }
};
