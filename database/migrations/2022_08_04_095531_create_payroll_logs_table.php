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
        Schema::create('payroll_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('consecutive');
            $table->string('status');
            $table->string('statusCode');
            $table->string('ErrorMessage');
            $table->string('cune');
            $table->foreignId('send_by')->constrained('users');
            $table->string('entity');
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
        Schema::dropIfExists('payroll_logs');
    }
};
