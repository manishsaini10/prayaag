<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->text('email'); // Encrypted
            $table->string('email_hash')->unique(); // SHA256 of lowercase email for fast lookups
            $table->string('name')->nullable();
            $table->string('status')->default('pending')->index(); // pending, subscribed, unsubscribed, bounced
            $table->string('confirm_token')->nullable()->unique();
            $table->string('consent_source')->nullable();
            $table->timestamp('consent_at')->nullable();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
    }
};
