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
        Schema::create('design_requirement_art_versions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('version')->default(1);

            $table->foreignId('art_id')->constrained('design_requirement_art');

            $table->string('line_code', 255)->collation('Modern_Spanish_CI_AS');
            $table->foreign('line_code')->references('code')->on('encoder_lines');

            $table->string('subline_code', 255)->collation('Modern_Spanish_CI_AS');
            $table->foreign('subline_code')->references('code')->on('encoder_sublines');

            $table->string('feature_code', 255)->collation('Modern_Spanish_CI_AS');
            $table->foreign('feature_code')->references('code')->on('encoder_features');

            $table->foreignId('material_id')->constrained('encoder_materials');

            $table->foreignId('measurement_id')->constrained('encoder_measurements');

            $table->longText('path2D')->nullable();

            $table->longText('path3D')->nullable();

            $table->foreignId('blueprint_id')->nullable()->constrained();

            $table->foreignId('brand_id')->constrained('brands');

            $table->float('weight')->nullable()->default(0);

            $table->foreignId('designer_id')->nullable()->constrained('users');

            $table->foreignId('seller_id')->nullable()->constrained('users');

            $table->longText('comments')->nullable();

            $table->longText('features_detail')->nullable();

            $table->boolean('enabled')->default(0);

            $table->enum('state', ['0', '1', '2', '3']);

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
        Schema::dropIfExists('design_requirement_art_versions');
    }
};
