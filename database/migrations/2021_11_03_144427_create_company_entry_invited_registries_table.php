<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyEntryInvitedRegistriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_entry_invited_registries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document')->constrained('company_entry_invited', 'document');
            $table->dateTime('entry');
            $table->dateTime('exit')->nullable();
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
        Schema::dropIfExists('company_entry_invited_registries');
    }
}
