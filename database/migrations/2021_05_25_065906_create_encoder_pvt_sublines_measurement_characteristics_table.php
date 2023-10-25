<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEncoderPvtSublinesMeasurementCharacteristicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encoder_pvt_sublines_measurement_characteristics', function (Blueprint $table) {
            $table->id();
            $table->string('sl_code');
            $table->foreign('sl_code')->references('code')->on('encoder_sublines');
            $table->string('mc_code');
            $table->foreign('mc_code')->references('code')->on('encoder_measurement_characteristics');
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
        Schema::dropIfExists('encoder_pvt_sublines_measurement_characteristics');
    }
}
