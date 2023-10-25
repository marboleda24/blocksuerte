<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeaderOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('header_orders', function (Blueprint $table) {
            $table->id();
            $table->string('oc')->nullable();
            $table->string('customer_code');
            $table->longText('notes')->nullable();
            $table->float('bruto', 50, 2);
            $table->float('subtotal', 50, 2);
            $table->float('taxes', 50, 2);
            $table->float('discount', 50, 2);
            $table->boolean('taxable');
            $table->enum('currency', ['COP', 'USD']);
            $table->enum('type', ['national', 'export', 'forecast', 'samples', 'elena', 'point_of_sale', 'services']);
            $table->bigInteger('order_max')->nullable();
            $table->enum('state', ['0', '1', '2', '3', '4', '5', '6', '7', '10']);
            $table->enum('destiny', ['P', 'C', 'D']);
            $table->string('substate')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->unsignedBigInteger('seller_id');
            $table->foreign('seller_id')->references('id')->on('users');

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
        Schema::dropIfExists('header_orders');
    }
}
