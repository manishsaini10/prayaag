<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_testimonial_tags', function (Blueprint $table) {
            $table->id(); // BIGINT — high-volume tagging, not a business identity table
            $table->ulid('video_testimonial_id');
            $table->enum('tag_type', ['program', 'event', 'class', 'department', 'custom']);
            $table->string('tag_value'); // e.g. "Admissions 2026-27", "Sports Day"
            $table->timestamps();

            $table->foreign('video_testimonial_id')
                  ->references('id')
                  ->on('video_testimonials')
                  ->cascadeOnDelete();

            $table->index(['tag_type', 'tag_value']);
            $table->index('video_testimonial_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_testimonial_tags');
    }
};
