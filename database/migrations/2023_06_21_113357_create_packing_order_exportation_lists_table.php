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
        Schema::create('packing_order_exportation_lists', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('consecutive')->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->json('data');
            $table->json('box_list');
            $table->enum('state', ['pending', 'close', 'cancel']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packing_order_exportation_lists');
    }
};
