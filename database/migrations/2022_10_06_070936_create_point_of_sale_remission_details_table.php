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
        Schema::create('point_of_sale_remission_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('header_id')->references('id')->on('point_of_sale_remission_headers');
            $table->string('product');
            $table->float('quantity');
            $table->float('price', 50, 2);
            $table->enum('unit_measurement', ['units', 'thousands', 'kilo']);
            $table->string('art')->default('G03661')->nullable();
            $table->string('art2')->nullable();
            $table->string('brand')->default('GENERICO')->nullable();
            $table->longText('notes')->nullable();
            $table->string('warehouse')->nullable();
            $table->enum('type', ['new', 'reprogrammed']);
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
        Schema::dropIfExists('point_of_sale_remission_details');
    }
};
