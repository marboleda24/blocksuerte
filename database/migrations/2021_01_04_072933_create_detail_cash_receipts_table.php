<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailCashReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_cash_receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cash_receipt_id');
            $table->foreign('cash_receipt_id')->references('id')->on('header_cash_receipts');
            $table->bigInteger('invoice');
            $table->float('bruto', 50, 2);
            $table->float('discount', 50, 2);
            $table->float('retention', 50, 2);
            $table->float('reteiva', 50, 2);
            $table->float('reteica', 50, 2);
            $table->float('other_deductions', 50, 2);
            $table->float('other_income', 50, 2);
            $table->float('total', 50, 2);
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
        Schema::dropIfExists('detail_cash_receipts');
    }
}
