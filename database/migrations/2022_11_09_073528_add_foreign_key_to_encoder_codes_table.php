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
        Schema::table('encoder_codes', function (Blueprint $table) {
            $table->unique([
                'product_type_code', 'line_code', 'subline_code', 'feature_code', 'material_id', 'measurement_id', 'galvanic_finish_code', 'decorative_option_code', 'art_code',
            ], 'unique_compose_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('encoder_codes', function (Blueprint $table) {
            //
        });
    }
};
