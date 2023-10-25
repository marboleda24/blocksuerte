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
        Schema::create('blueprint_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blueprint_id')->constrained();
            $table->longText('path');
            $table->longText('miniature');
            $table->foreignId('upload_by')->constrained('users');
            $table->integer('version');
            $table->integer('state')->default(0);
            $table->longText('details')->nullable();
            $table->enum('type', ['TRQ', 'PRD']);
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
        Schema::dropIfExists('blueprint_files');
    }
};
