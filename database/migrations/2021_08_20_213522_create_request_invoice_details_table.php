<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestInvoiceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_invoice_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_invoice_id');
            $table->foreign('request_invoice_id')->references('id')->on('request_invoices');
            $table->string('item');
            $table->string('product_code');
            $table->float('quantity', 50, 2)->nullable();
            $table->longText('notes')->nullable();
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
        Schema::dropIfExists('request_invoice_details');
    }
}
