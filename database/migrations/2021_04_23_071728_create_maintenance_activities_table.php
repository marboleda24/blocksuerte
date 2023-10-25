<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_id');
            $table->foreign('work_order_id')->references('id')->on('maintenance_work_orders');
            $table->unsignedBigInteger('work_type_id');
            $table->foreign('work_type_id')->references('id')->on('maintenance_work_types');
            $table->unsignedBigInteger('assigned_to');
            $table->foreign('assigned_to')->references('id')->on('users');
            $table->float('cost')->nullable();
            $table->enum('state', ['1', '2', '3']);
            $table->longText('description');
            $table->longText('conclusion')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamp('finish_date')->nullable();
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
        Schema::dropIfExists('maintenance_activities');
    }
}
