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
        Schema::create('claim_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('header_id')->constrained('claim_headers');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('type', ['log', 'comment']);
            $table->string('description');
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
        Schema::dropIfExists('claim_logs');
    }
};
