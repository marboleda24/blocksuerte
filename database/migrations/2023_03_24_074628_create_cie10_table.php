<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cie10', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('symbol')->nullable();
            $table->string('description');
            $table->string('group');
            $table->string('segment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cie10');
    }
};
