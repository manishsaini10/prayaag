<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * URL redirects (301/302) — essential for SEO when migrating old WordPress
 * URLs. Matched by HandleRedirects middleware on incoming GET requests.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('redirects', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('from_path');
            $table->string('to_path');
            $table->integer('status_code')->default(301);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('hits')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->unique('from_path');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redirects');
    }
};
