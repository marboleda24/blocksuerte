<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachinesBinnacle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machines_binnacle', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->string('brand');
            $table->float('btu_tc', 50, 2);
            $table->float('kcal_tc', 50, 2);
            $table->enum('type', ['P', 'H']);
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
        Schema::dropIfExists('machines_binnacle');
    }
}
