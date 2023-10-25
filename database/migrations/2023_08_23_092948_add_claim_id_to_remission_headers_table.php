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
        Schema::table('remission_headers', function (Blueprint $table) {
            $table->foreignId('claim_id')->nullable()->constrained('claim_headers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('remission_headers', function (Blueprint $table) {
            //
        });
    }
};
