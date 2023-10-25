<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemissionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('remission_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('remission_header_id');
            $table->foreign('remission_header_id')->references('id')->on('remission_headers');
            $table->string('product');
            $table->float('quantity');
            $table->float('price', 50, 2);
            $table->enum('unit_measurement', ['units', 'thousands', 'kilo']);
            $table->string('art')->default('LISO')->nullable();
            $table->string('art2')->nullable();
            $table->string('brand')->default('GENERICO')->nullable();
            $table->longText('notes')->nullable();
            $table->string('customer_product_code')->nullable();
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
        Schema::dropIfExists('remission_details');
    }
}
