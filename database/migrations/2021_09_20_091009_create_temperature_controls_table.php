<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemperatureControlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temperature_controls', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('employee_document');
            $table->float('temperature');
            $table->string('fever');
            $table->string('cough');
            $table->string('throat_pain');
            $table->string('respiratory_distress');
            $table->string('loss_of_taste');
            $table->string('contact_infected_person');
            $table->longText('observations')->nullable();
            $table->timestamp('time_of_entry');
            $table->integer('created_by');
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
        Schema::dropIfExists('temperature_controls');
    }
}
