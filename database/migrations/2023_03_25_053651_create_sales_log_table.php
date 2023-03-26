<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_id');
            $table->foreign('sales_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->double('lat')->nullable();
            $table->double('lng')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_log');
    }
}
