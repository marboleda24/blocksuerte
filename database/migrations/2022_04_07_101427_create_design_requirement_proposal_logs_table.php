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
        Schema::create('design_requirement_proposal_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('design_requirement_proposals');
            $table->longText('description');
            $table->foreignId('created_by')->constrained('users');
            $table->string('type');
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
        Schema::dropIfExists('design_requirement_proposal_logs');
    }
};
