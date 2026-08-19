<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_providers_config', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('provider_key'); // smtp, hostinger, zoho, brevo, elastic_email, mailjet, ses, log
            $table->string('label');
            $table->text('credentials'); // Encrypted JSON
            $table->boolean('is_active')->default(false);
            $table->integer('priority_order')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('last_tested_at')->nullable();
            $table->integer('failure_count')->default(0);
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_providers_config');
    }
};
