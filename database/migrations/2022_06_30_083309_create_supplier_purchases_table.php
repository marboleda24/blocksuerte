<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_purchases', function (Blueprint $table) {
            $table->id();
            $table->json('application_response');
            $table->json('document_information');
            $table->json('customer');
            $table->json('supplier');
            $table->json('payment_means');
            $table->json('payment_terms');
            $table->json('allowance_charge');
            $table->json('legal_monetary_total');
            $table->json('tax_total');
            $table->json('items');
            $table->string('pdf_path');
            $table->string('xml_path');
            $table->foreignId('upload_by')->constrained('users');
            $table->foreignId('received_by')->nullable()->constrained('users');
            $table->foreignId('accepted_by')->nullable()->constrained('users');
            $table->string('state');
            $table->string('work_center')->nullable();
            $table->string('classification')->nullable();
            $table->string('dian_state');
            $table->enum('entity', ['CIEV', 'GOJA'])->default('CIEV');
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
        Schema::dropIfExists('supplier_purchases');
    }
};
