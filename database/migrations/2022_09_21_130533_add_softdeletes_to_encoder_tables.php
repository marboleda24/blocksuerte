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
        Schema::table('encoder_product_types', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('encoder_lines', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('encoder_sublines', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('encoder_features', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('encoder_materials', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('encoder_measurements', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('encoder_decorative_options', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('encoder_galvanic_finishes', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('encoder_codes', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('encoder_product_types', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('encoder_lines', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('encoder_sublines', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('encoder_features', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('encoder_materials', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('encoder_measurements', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('encoder_decorative_options', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('encoder_galvanic_finishes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('encoder_codes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
