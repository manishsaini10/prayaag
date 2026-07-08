<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 404 monitoring (Phase 14). One row per distinct missing path, with a hit
 * counter and the latest referrer/user-agent. Drives the admin 404 report and
 * one-click redirect creation. Populated by the exception render hook.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('not_found_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('path');
            $table->unsignedInteger('hits')->default(1);
            $table->string('referrer')->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->boolean('resolved')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->unique('path');
            $table->index(['resolved', 'hits']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('not_found_logs');
    }
};
