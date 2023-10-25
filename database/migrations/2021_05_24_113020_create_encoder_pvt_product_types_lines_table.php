<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEncoderPvtProductTypesLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encoder_pvt_product_types_lines', function (Blueprint $table) {
            $table->id();
            $table->string('product_type_code');
            $table->foreign('product_type_code')->references('code')->on('encoder_product_types');
            $table->string('line_code');
            $table->foreign('line_code')->references('code')->on('encoder_lines');
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
        Schema::dropIfExists('encoder_pvt_product_types_lines');
    }
}
