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
        Schema::create('support_document_headers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('consecutive')->unique();
            $table->string('seller_document');
            $table->longText('notes')->nullable();
            $table->date('transaction_date');
            $table->foreignId('created_id')->constrained('users');
            $table->string('payment_form');
            $table->enum('state', ['pending', 'success', 'canceled'])->default('pending');
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
        Schema::dropIfExists('support_document_headers');
    }
};
