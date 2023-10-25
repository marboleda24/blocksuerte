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
        Schema::create('design_requirement_headers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users');
            $table->string('customer_code');
            $table->foreignId('brand_id')->constrained('brands');
            $table->string('parameter');

            $table->string('product_type_code', 255)->collation('Modern_Spanish_CI_AS');
            $table->foreign('product_type_code')->references('code')->on('encoder_product_types');

            $table->string('line_code', 255)->collation('Modern_Spanish_CI_AS');
            $table->foreign('line_code')->references('code')->on('encoder_lines');

            $table->string('subline_code', 255)->collation('Modern_Spanish_CI_AS');
            $table->foreign('subline_code')->references('code')->on('encoder_sublines');

            $table->string('feature_code', 255)->collation('Modern_Spanish_CI_AS');
            $table->foreign('feature_code')->references('code')->on('encoder_features');

            $table->foreignId('material_id')->constrained('encoder_materials');

            $table->foreignId('measurement_id')->constrained('encoder_measurements');

            $table->string('base')->default('no');
            $table->longText('details');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('assigned_designer_id')->nullable()->constrained('users');
            $table->enum('state', ['0', '1', '2', '3', '4', '5', '6', '7'])->default('1');
            $table->enum('render', ['yes', 'no']);
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
        Schema::dropIfExists('design_requirement_headers');
    }
};
