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
        Schema::create('support_document_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_document_header_id')->constrained('support_document_headers');
            $table->foreignId('product_id')->constrained('support_document_products');
            $table->string('type_transmition_id');
            $table->date('transmition_date')->nullable();
            $table->float('price');
            $table->float('quantity');
            $table->float('retention');
            $table->string('measurement');
            $table->string('type');
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
        Schema::dropIfExists('support_document_details');
    }
};
