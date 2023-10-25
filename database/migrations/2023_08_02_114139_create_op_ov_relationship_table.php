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
        Schema::create('op_ov_relationship', function (Blueprint $table) {
            $table->id();
            $table->string('op');
            $table->string('ov');
            $table->string('item');
            $table->timestamps();
            $table->unique(['op', 'ov', 'item']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('op_ov_relationship');
    }
};
