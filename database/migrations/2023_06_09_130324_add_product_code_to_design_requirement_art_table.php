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
        Schema::table('design_requirement_art', function (Blueprint $table) {
            $table->string('archaic_product_code', 255)->nullable()->collation('Modern_Spanish_CI_AS');
            $table->foreign('archaic_product_code')->references('code')->on('encoder_codes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('design_requirement_art', function (Blueprint $table) {
            //
        });
    }
};
