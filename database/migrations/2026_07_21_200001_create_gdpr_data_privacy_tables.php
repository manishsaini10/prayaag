<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_privacy_requests', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('email');
            $table->string('request_type'); // export, delete, rectify
            $table->string('status')->default('pending'); // pending, verified, processing, completed, rejected
            $table->string('verification_token')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->string('processed_by')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('export_file_path', 500)->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('status');
        });

        Schema::create('consent_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('email')->nullable();
            $table->string('consentable_type')->nullable();
            $table->string('consentable_id')->nullable();
            $table->string('consent_type');
            $table->text('consent_text');
            $table->string('ip_address')->nullable();
            $table->timestamp('given_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('email');
            $table->index(['consentable_type', 'consentable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consent_logs');
        Schema::dropIfExists('data_privacy_requests');
    }
};
