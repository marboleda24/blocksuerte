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
        Schema::create('claim_headers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('consecutive')->unique();
            $table->enum('destiny', ['cellar', 'quality']);
            $table->enum('action', ['credit-note', 'change-reposition', 'manufacturing', 'reprocess', 'return']);
            $table->enum('reason', ['change', 'reposition', 'quantity', 'date', 'price', 'discount', 'NA', 'major-value', 'customer-change', 'quantity-new-invoice']);
            $table->bigInteger('document');
            $table->longText('notes')->nullable();
            $table->foreignId('created_id')->constrained('users');
            $table->foreignId('workplace_id')->nullable()->constrained('claim_workplaces');
            $table->enum('state', ['erase', 'refuse', 'quality', 'cellar', 'wallet', 'finish', 'canceled']);
            $table->string('production_order')->nullable();
            $table->longText('quality_observation')->nullable();
            $table->longText('cellar_observation')->nullable();
            $table->bigInteger('credit_memo')->nullable();
            $table->bigInteger('sale_order')->nullable();
            $table->foreignId('remission_id')->nullable()->constrained('remission_headers');
            $table->float('discount', 10, 2)->nullable();
            $table->float('major_value', 10, 2)->nullable();
            $table->string('new_customer_code')->nullable();
            $table->string('credit_note')->nullable();
            $table->boolean('accounted')->default(false);
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
        Schema::dropIfExists('claim_headers');
    }
};
