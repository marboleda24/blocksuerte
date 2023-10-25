<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEncoderMeasurementDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encoder_measurement_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('measurement_id');
            $table->foreign('measurement_id')->references('id')->on('encoder_measurements');

            $table->string('unit_code');
            $table->foreign('unit_code')->references('code')->on('encoder_unit_measurements');

            $table->string('characteristic_code');
            $table->foreign('characteristic_code')->references('code')->on('encoder_measurement_characteristics');

            $table->float('value', 50, 8);
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
        Schema::dropIfExists('encoder_measurement_details');
    }
}
