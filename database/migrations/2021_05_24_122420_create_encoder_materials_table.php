<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEncoderMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encoder_materials', function (Blueprint $table) {
            $table->id();
            $table->string('line_code');
            $table->foreign('line_code')->references('code')->on('encoder_lines');
            $table->string('subline_code');
            $table->foreign('subline_code')->references('code')->on('encoder_sublines');
            $table->string('material_code');
            $table->foreign('material_code')->references('code')->on('encoder_material_exts');
            $table->longText('comments')->nullable();
            $table->boolean('state')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
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
        Schema::dropIfExists('encoder_materials');
    }
}
