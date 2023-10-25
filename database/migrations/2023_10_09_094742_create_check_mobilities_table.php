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
        Schema::create('check_mobilities', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('city');
            $table->string('driver');
            $table->string('boss');
            $table->string('mileage');
            $table->string('plate');
            $table->json('documents');
            $table->json('inspection');
            $table->json('road_kit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_mobilities');
    }
};
