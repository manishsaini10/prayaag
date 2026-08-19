<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // BIGINT PK — analytics table, very high write volume
        Schema::create('video_testimonial_views', function (Blueprint $table) {
            $table->id();
            $table->ulid('video_testimonial_id');
            $table->string('session_id', 64);               // hashed/anonymous — never raw session or user ID
            $table->unsignedTinyInteger('watch_percentage')->default(0); // 0–100
            $table->enum('device_type', ['mobile', 'tablet', 'desktop'])->default('desktop');
            $table->boolean('cta_clicked')->default(false);
            $table->timestamp('viewed_at');

            $table->foreign('video_testimonial_id')
                  ->references('id')
                  ->on('video_testimonials')
                  ->cascadeOnDelete();

            $table->index(['video_testimonial_id', 'viewed_at']);
            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_testimonial_views');
    }
};
