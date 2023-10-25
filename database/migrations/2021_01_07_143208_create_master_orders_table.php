<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cellar')->nullable();
            $table->foreign('cellar')->references('id')->on('header_orders');

            $table->unsignedBigInteger('production')->nullable();
            $table->foreign('production')->references('id')->on('header_orders');

            $table->unsignedBigInteger('dies')->nullable();
            $table->foreign('dies')->references('id')->on('header_orders');

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
        Schema::dropIfExists('master_orders');
    }
}
