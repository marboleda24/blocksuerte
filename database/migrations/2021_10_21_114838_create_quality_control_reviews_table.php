<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQualityControlReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quality_control_reviews', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('production_order');
            $table->bigInteger('quantity_inspected');
            $table->bigInteger('conforming_quantity');
            $table->bigInteger('non_conforming_quantity');

            $table->unsignedBigInteger('cause_id');
            $table->foreign('cause_id')->references('id')->on('quality_control_review_causes');

            $table->unsignedBigInteger('operator_id');
            $table->foreign('operator_id')->references('id')->on('users');

            $table->unsignedBigInteger('inspector_id');
            $table->foreign('inspector_id')->references('id')->on('users');

            $table->longText('non_compliant_treatment');
            $table->longText('actions');
            $table->longText('observations');
            $table->string('work_center');

            $table->enum('type', ['production', 'inspection']);

            $table->unsignedBigInteger('register_by');
            $table->foreign('register_by')->references('id')->on('users');

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
        Schema::dropIfExists('quality_control_reviews');
    }
}
