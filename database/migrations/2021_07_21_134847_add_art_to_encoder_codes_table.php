<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArtToEncoderCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('encoder_codes', function (Blueprint $table) {
            $table->string('art_code')->after('decorative_option_code')->nullable();
            $table->foreign('art_code')->references('code')->on('design_requirement_arts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('encoder_codes', function (Blueprint $table) {
            //
        });
    }
}
