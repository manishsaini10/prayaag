<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('job_listing_id')->constrained('job_listings')->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->longText('cover_letter')->nullable();
            $table->foreignUlid('resume_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('status')->default('new'); // new | reviewing | rejected | hired
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('job_listing_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
