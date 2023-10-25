<?php

use App\Models\DesignRequirementHeader;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('design_requirement_products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();
            $table->string('description')->unique();

            $table->foreignId('father_id')->nullable()->constrained('design_requirement_products');

            $table->string('art_code')->nullable();
            $table->foreign('art_code')->references('code')->on('design_requirement_art');

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

            $table->foreignId('brand_id')->nullable()->constrained('brands');

            $table->enum('type', ['father', 'child', 'final'])->default('child');
            $table->enum('state', ['pending', 'clone', 'finish', 'NA'])->default('na');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_requirement_products');
    }
};
