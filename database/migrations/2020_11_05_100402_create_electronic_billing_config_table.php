<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElectronicBillingConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('electronic_billing_config', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('invoice_numeration');
            $table->integer('invoice_environment');
            $table->bigInteger('invoice_report');
            $table->bigInteger('exports_invoice_report');

            $table->bigInteger('credit_note_numeration');
            $table->integer('credit_note_environment');
            $table->bigInteger('credit_note_report');
            $table->bigInteger('exports_credit_note_report');
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
        Schema::dropIfExists('electronic_billing_config');
    }
}
