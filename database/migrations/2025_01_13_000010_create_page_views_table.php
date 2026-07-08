<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Lightweight first-party analytics: one row per public page view.
        Schema::create('page_views', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('page_id')->nullable()->constrained('pages')->nullOnDelete();
            $table->string('path');
            $table->string('referrer')->nullable();
            $table->string('ip_hash', 64)->nullable(); // hashed, never raw IP
            $table->string('user_agent', 512)->nullable();
            $table->timestamp('viewed_at');
            $table->index('viewed_at');
            $table->index('page_id');
            $table->index('path'); // top-pages analytics aggregation
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
