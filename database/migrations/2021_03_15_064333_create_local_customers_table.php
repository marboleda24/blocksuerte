<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocalCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('local_customers', function (Blueprint $table) {
            $table->id();
            $table->enum('document_type', ['C', 'N']);
            $table->bigInteger('document');
            $table->enum('customer_type', ['PJ', 'PN']);
            $table->enum('type_legal_entity', ['RC', 'CI', 'ZF']);
            $table->string('first_name')->nullable();
            $table->string('second_name')->nullable();
            $table->string('surname')->nullable();
            $table->string('second_surname')->nullable();
            $table->string('business_name')->nullable();
            $table->string('business_reason')->nullable();
            $table->string('country');
            $table->string('province');
            $table->string('city');
            $table->string('address');
            $table->bigInteger('phone')->nullable();
            $table->bigInteger('cellphone')->nullable();
            $table->string('email');
            $table->unsignedBigInteger('seller_id');
            $table->foreign('seller_id')->references('id')->on('users');
            $table->string('main_activity');
            $table->boolean('great_contributor');
            $table->boolean('responsable_iva');
            $table->float('credit_limit', 50, 2)->nullable();
            $table->string('payment_deadline')->nullable();
            $table->integer('discount_rate')->nullable();
            $table->string('email_fe');
            $table->longText('emails_copies_fe')->nullable();
            $table->text('rut_file')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users');
            $table->enum('state', ['0', '1', '2']);
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
        Schema::dropIfExists('local_customers');
    }
}
