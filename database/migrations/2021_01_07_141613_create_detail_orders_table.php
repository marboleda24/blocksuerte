<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('header_orders');
            $table->string('product');
            $table->float('quantity');
            $table->float('price', 50, 2);
            $table->enum('unit_measurement', ['units', 'thousands', 'kilo']);
            $table->string('art')->default('LISO')->nullable();
            $table->string('art2')->nullable();
            $table->string('brand')->default('GENERICO')->nullable();
            $table->longText('notes')->nullable();
            $table->string('customer_product_code')->nullable();
            $table->enum('type', ['new', 'reprogrammed']);
            $table->enum('destiny', ['P', 'C', 'D'])->comment('p - produccion, c - bodega, d - troqueles');
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
        Schema::dropIfExists('detail_orders');
    }
}
