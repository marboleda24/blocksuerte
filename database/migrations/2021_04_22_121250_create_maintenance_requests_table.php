<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('applicant_id');
            $table->foreign('applicant_id')->references('id')->on('users');
            $table->unsignedBigInteger('asset_id');
            $table->foreign('asset_id')->references('id')->on('maintenance_assets');
            $table->date('planning_date');
            $table->longText('description');
            $table->enum('type', ['preventive', 'corrective', 'locative', 'improvement']);
            $table->enum('state', ['0', '1', '2', '3', '4', '5'])->comment('0: anulado, 1: en revision, 2: en proceso, 3: aprobada, ');
            $table->date('closing_date')->nullable();
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
        Schema::dropIfExists('maintenance_requests');
    }
}
