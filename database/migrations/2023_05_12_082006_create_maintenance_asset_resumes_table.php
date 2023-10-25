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
        Schema::create('maintenance_asset_resumes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('maintenance_assets');
            $table->string('model');
            $table->string('brand');
            $table->float('power')->nullable();
            $table->float('amperage')->nullable();
            $table->float('voltage')->nullable();
            $table->float('frequency')->nullable();
            $table->float('watts')->nullable();
            $table->float('rpm')->nullable();
            $table->foreignId('created_id')->constrained('users');
            $table->foreignId('updated_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_asset_resumes');
    }
};
