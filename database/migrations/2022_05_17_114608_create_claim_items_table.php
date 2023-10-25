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
        Schema::create('claim_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('header_id')->constrained('claim_headers');
            $table->string('item');
            $table->string('product_code');
            $table->string('new_product_code')->nullable();
            $table->float('new_price', 10, 2)->nullable();
            $table->float('credit_note_quantity', 10, 2)->nullable();
            $table->float('new_quantity', 10, 2)->nullable();
            $table->float('reposition_quantity', 10, 2)->nullable();
            $table->float('delivered_quantity', 10, 2)->nullable();
            $table->float('reprocessing_quantity', 10, 2)->nullable();
            $table->string('notes')->nullable();
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
        Schema::dropIfExists('claim_items');
    }
};
