<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Admin-facing notification feed (global to staff). Distinct from Laravel's
 * per-user `notifications` channel — these are CMS events surfaced in the
 * header bell + the Notifications page.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('type')->default('system');   // enquiry|application|subscriber|content|system|security|user
            $table->string('level')->default('info');     // info|success|warning|danger
            $table->string('title');
            $table->string('body')->nullable();
            $table->string('url')->nullable();
            $table->string('icon')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['read_at', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
