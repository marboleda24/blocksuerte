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
        Schema::create('other_users', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('nick');
            $table->string('name');
            $table->boolean('reviewer')->default(false);
            $table->boolean('packer')->default(false);
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
        Schema::dropIfExists('other_users');
    }
};
