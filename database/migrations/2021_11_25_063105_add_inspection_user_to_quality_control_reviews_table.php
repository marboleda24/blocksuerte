<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInspectionUserToQualityControlReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quality_control_reviews', function (Blueprint $table) {
            $table->foreignId('inspection_user_id')->nullable()->after('type')->constrained('quality_control_inspection_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quality_control_reviews', function (Blueprint $table) {
            $table->removeColumn('inspection_user_id');
        });
    }
}
