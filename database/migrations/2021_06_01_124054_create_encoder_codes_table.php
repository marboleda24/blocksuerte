<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEncoderCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encoder_codes', function (Blueprint $table) {
            $table->string('code')->primary();
            $table->string('description')->unique();
            $table->longText('comments')->nullable();
            $table->boolean('generic')->default(true);
            $table->boolean('state')->default(true);

            $table->string('product_type_code');
            $table->foreign('product_type_code')->references('code')->on('encoder_product_types');

            $table->string('line_code');
            $table->foreign('line_code')->references('code')->on('encoder_lines');

            $table->string('subline_code');
            $table->foreign('subline_code')->references('code')->on('encoder_sublines');

            $table->string('feature_code');
            $table->foreign('feature_code')->references('code')->on('encoder_features');

            $table->unsignedBigInteger('material_id');
            $table->foreign('material_id')->references('id')->on('encoder_materials');

            $table->unsignedBigInteger('measurement_id');
            $table->foreign('measurement_id')->references('id')->on('encoder_measurements');

            $table->string('galvanic_finish_code')->nullable();
            $table->foreign('galvanic_finish_code')->references('code')->on('encoder_galvanic_finishes');

            $table->string('decorative_option_code')->nullable();
            $table->foreign('decorative_option_code')->references('code')->on('encoder_decorative_options');

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
        Schema::dropIfExists('encoder_codes');
    }
}
