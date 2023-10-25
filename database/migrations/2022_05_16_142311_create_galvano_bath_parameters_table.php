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
        Schema::create('galvano_bath_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('production_order');
            $table->string('product_code');
            $table->string('bath');
            $table->float('ph', 10);
            $table->float('density', 10);
            $table->float('temperature', 10);
            $table->time('entry_time');
            $table->time('exit_time');
            $table->foreignId('user_id')->constrained('users');
            $table->longText('notes')->nullable();
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
        Schema::dropIfExists('galvano_bath_parameters');
    }
};
