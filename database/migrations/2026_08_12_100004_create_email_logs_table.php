<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('template_key')->index();
            $table->string('module')->nullable()->index();
            $table->string('provider_used')->nullable();
            $table->text('to_address'); // Encrypted
            $table->string('subject');
            $table->string('status')->default('queued')->index(); // queued, sent, failed, bounced, complained
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
