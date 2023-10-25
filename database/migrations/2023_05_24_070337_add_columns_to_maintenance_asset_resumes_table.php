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
        Schema::table('maintenance_asset_resumes', function (Blueprint $table) {
            $table->bigInteger('maintenance_frequency')->nullable();
            $table->string('dimension')->nullable();
            $table->longText('preventive_maintenance_description')->nullable();
            $table->longText('precautions')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_asset_resumes', function (Blueprint $table) {
            //
        });
    }
};
