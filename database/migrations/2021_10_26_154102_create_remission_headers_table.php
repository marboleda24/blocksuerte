<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemissionHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('remission_headers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code');
            $table->longText('notes')->nullable();
            $table->float('bruto', 50, 2);
            $table->float('subtotal', 50, 2);
            $table->float('taxes', 50, 2);
            $table->float('discount', 50, 2);
            $table->string('oc')->nullable();
            $table->enum('currency', ['COP', 'USD']);
            $table->enum('type_sale', ['sale', 'service']);
            $table->enum('state', ['0', '1', '2']);

            $table->bigInteger('document_support')->nullable();

            $table->bigInteger('order_number')->nullable();
            $table->bigInteger('order_max')->unique()->nullable();

            $table->foreignId('type_id')->constrained('remission_types');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('seller_id')->constrained('users');

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
        Schema::dropIfExists('remission_headers');
    }
}
