<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_testimonials', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('title');
            $table->string('student_name')->nullable();
            $table->string('class_grade')->nullable();           // e.g. "Grade 8"
            $table->string('submitted_by_name')->nullable();
            $table->string('submitted_by_email')->nullable();
            $table->string('submitted_by_phone')->nullable();

            // Provider-agnostic video reference
            $table->string('video_provider');                    // 'youtube_unlisted' | 'cloudflare_stream' | 'local'
            $table->string('video_external_id');                 // provider's video ID
            $table->string('video_embed_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->enum('orientation', ['portrait', 'landscape'])->default('landscape');

            // Moderation
            $table->enum('status', ['pending', 'approved', 'rejected', 'archived'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->ulid('reviewed_by')->nullable();             // FK -> users (admin)
            $table->timestamp('reviewed_at')->nullable();

            // Consent (legally required for minors — do NOT skip)
            $table->boolean('consent_confirmed')->default(false);
            $table->string('consent_signed_by')->nullable();     // parent/guardian name
            $table->timestamp('consent_signed_at')->nullable();

            // CTA
            $table->string('cta_label')->nullable();             // "Apply Now"
            $table->string('cta_url')->nullable();

            // Display
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedBigInteger('views_count')->default(0);
            $table->boolean('is_featured')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'sort_order']);
            $table->index(['status', 'consent_confirmed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_testimonials');
    }
};
