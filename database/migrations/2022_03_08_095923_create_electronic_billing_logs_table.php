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
        Schema::create('electronic_billing_logs', function (Blueprint $table) {
            $table->id();
            $table->string('document');
            $table->string('status');
            $table->string('status_code');
            $table->string('error_message');
            $table->foreignId('send_id')->constrained('users');
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
        Schema::dropIfExists('electronic_billing_logs');
    }
};
