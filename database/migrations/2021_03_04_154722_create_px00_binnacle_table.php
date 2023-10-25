<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePx00BinnacleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('px00_binnacle', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('machine_id')->nullable();
            $table->foreign('machine_id')->references('id')->on('machines_binnacle');
            $table->date('date');
            $table->enum('workshift', ['1', '2', '3']);
            $table->float('tb', 50, 2);
            $table->float('rz', 50, 2);
            $table->float('vz', 50, 2);
            $table->float('z', 50, 2);
            $table->unsignedBigInteger('operator_id')->nullable();
            $table->foreign('operator_id')->references('id')->on('users');
            $table->enum('maintenance', ['preventive', 'corrective'])->nullable();
            $table->enum('type_maintenance', ['mechanical', 'hydraulic', 'pneumatic', 'electric'])->nullable();
            $table->unsignedBigInteger('maintenance_operator_id')->nullable();
            $table->foreign('maintenance_operator_id')->references('id')->on('users');
            $table->longText('observations')->nullable();
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
        Schema::dropIfExists('px00_binnacle');
    }
}
