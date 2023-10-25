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
        Schema::create('point_of_sale_remission_headers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('consecutive')->unique();
            $table->foreignId('order_id')->constrained('header_orders');
            $table->enum('location', ['ITAGUI', 'CALI', 'BOGOTA', 'PEREIRA']);
            $table->enum('state', ['pending', 'transit', 'success']);
            $table->foreignId('created_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('point_of_sale_remission_headers');
    }
};
