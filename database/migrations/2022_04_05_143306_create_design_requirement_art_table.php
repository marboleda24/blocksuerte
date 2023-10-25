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
        Schema::create('design_requirement_art', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->unsigned();
            $table->foreignId('design_requirement_id')->constrained('design_requirement_headers');
            $table->foreignId('proposal_id')->constrained('design_requirement_proposals');
            //$table->string('final_product_code', 255)->nullable()->collation('Modern_Spanish_CI_AS');
            //$table->foreign('final_product_code')->references('code')->on('encoder_codes');
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
        Schema::dropIfExists('design_requirement_art');
    }
};
