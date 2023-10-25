<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_invoices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_worksplace')->nullable();
            $table->bigInteger('invoice');
            $table->bigInteger('id_reason');
            $table->longText('comments')->nullable();
            $table->enum('state', ['0', '1', '2', '3', '4', '5', '6', '7']);
            $table->enum('process_status', ['0', '1', '2', '3', '4', '5', '6']);
            $table->String('new_invoice')->nullable();
            $table->String('file')->nullable();
            $table->string('file_approved')->nullable();
            $table->longText('justify')->nullable();
            $table->longText('justify_send_store')->nullable();
            $table->longText('justify_refuse_store')->nullable();
            $table->longText('reopen_quality_comments')->nullable();
            $table->longText('reopen_store_comments')->nullable();
            $table->longText('observations')->nullable();
            $table->longText('observations_quality')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
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
        Schema::dropIfExists('request_invoices');
    }
}
