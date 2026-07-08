<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Named job_listings (not jobs) to avoid colliding with Laravel's
        // queue `jobs` table from the default migration.
        Schema::create('job_listings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('title');
            $table->string('slug');
            $table->string('department')->nullable();
            $table->string('location')->nullable();
            $table->string('employment_type')->default('full_time'); // full_time | part_time | contract
            $table->longText('description')->nullable();
            $table->string('status')->default('open'); // open | closed
            $table->timestamp('closes_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique('slug');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
