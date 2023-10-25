<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVacationRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacation_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('employee_document');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->longText('justify')->nullable();
            $table->bigInteger('boss_document')->nullable();
            $table->timestamp('boss_approved_date')->nullable();

            $table->unsignedBigInteger('approved_human_resource')->nullable();
            $table->foreign('approved_human_resource')->references('id')->on('users');

            $table->enum('state', ['0', '1', '2', '3', '4', '5']);
            $table->longText('observations')->nullable();
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
        Schema::dropIfExists('vacation_requests');
    }
}
