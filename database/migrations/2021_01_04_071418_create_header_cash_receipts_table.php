<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeaderCashReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('header_cash_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code');
            $table->float('total_paid', 50, 2);
            $table->longText('comments')->nullable();
            $table->date('payment_date');
            $table->enum('payment_account', ['11200505', '11200510', '11200515', '11100505', '11100506']);
            $table->enum('payment_method', ['1', '7']);
            $table->longText('observations')->nullable();
            $table->bigInteger('dms_cash_receipt')->nullable();
            $table->enum('state', [0, 1, 2, 3, 4]);
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users');
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
        Schema::dropIfExists('header_cash_receipts');
    }
}
