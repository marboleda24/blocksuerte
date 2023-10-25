<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_assets', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->unsignedBigInteger('classification_id');
            $table->foreign('classification_id')->references('id')->on('maintenance_asset_classifications');
            $table->enum('state', ['good', 'repair', 'discarded']);
            $table->unsignedBigInteger('work_center_id');
            $table->foreign('work_center_id')->references('id')->on('maintenance_work_centers');
            $table->enum('priority', ['critical', 'normal', 'low']);
            $table->dateTime('last_revision')->nullable();
            $table->longText('comments');
            $table->json('data_sheet')->nullable();
            $table->longText('functionality')->nullable();
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
        Schema::dropIfExists('maintenance_assets');
    }
}
