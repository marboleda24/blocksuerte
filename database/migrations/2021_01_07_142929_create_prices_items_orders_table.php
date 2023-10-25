<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricesItemsOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices_items_orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code');
            $table->string('product');
            $table->string('customer_product_code')->nullable();
            $table->float('price', 50, 2);
            $table->unsignedBigInteger('approved_by');
            $table->foreign('approved_by')->references('id')->on('users');
            $table->longText('notes')->nullable();
            $table->boolean('state');
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
        Schema::dropIfExists('prices_items_orders');
    }
}
