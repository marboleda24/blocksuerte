<?php

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
        Schema::create('postmark_mail_logs', function (Blueprint $table) {
            $table->id();
            $table->string('Server');
            $table->string('MessageID')->unique();
            $table->string('MessageStream');
            $table->json('To');
            $table->json('Cc');
            $table->json('Bcc');
            $table->json('Recipients');
            $table->dateTime('ReceivedAt');
            $table->string('From');
            $table->string('Subject');
            $table->json('Attachments');
            $table->string('Status');
            $table->boolean('TrackOpens');
            $table->json('MessageEvents');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postmark_mail_logs');
    }
};
