<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_incomes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('document');
            $table->string('fist_name')->nullable();
            $table->string('second_name')->nullable();
            $table->string('first_lastname')->nullable();
            $table->string('second_lastname')->nullable();
            $table->string('gender')->nullable();
            $table->date('birthday')->nullable();
            $table->string('blood_type')->nullable();
            $table->dateTime('entry_datetime');
            $table->dateTime('exit_datetime')->nullable();
            $table->enum('type', ['employee', 'guest']);
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
        Schema::dropIfExists('employee_incomes');
    }
};
