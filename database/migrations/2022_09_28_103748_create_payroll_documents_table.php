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
    public function up(): void
    {
        Schema::create('payroll_documents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('employee_document');
            $table->bigInteger('consecutive');
            $table->enum('entity', ['CIEV', 'GOJA']);
            $table->json('payload');
            $table->bigInteger('year');
            $table->bigInteger('month');
            $table->bigInteger('start_period');
            $table->bigInteger('end_period');
            $table->enum('status', ['pending', 'success', 'failed']);
            $table->enum('type_operation', ['payroll', 'adjust', 'destroy']);
            $table->bigInteger('document_reference')->nullable();
            $table->timestamps();
            $table->unique(['employee_document', 'consecutive', 'entity', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_documents');
    }
};
