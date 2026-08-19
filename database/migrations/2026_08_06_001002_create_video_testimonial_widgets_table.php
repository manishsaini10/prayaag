<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_testimonial_widgets', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('page_widget_id'); // FK -> page_widgets (existing table)
            $table->enum('layout_style', ['grid', 'carousel', 'story_bubble', 'spotlight_modal'])->default('grid');
            $table->json('settings')->nullable();
            // settings JSON shape:
            // {
            //   "autoplay": false, "muted": true, "max_videos": 12,
            //   "filter_tag_type": "program", "filter_tag_value": "Admissions 2026-27",
            //   "carousel_style": "default|spotlight", "show_cta": true,
            //   "position": "bottom-right"  <-- for story_bubble only
            // }
            $table->timestamps();

            $table->foreign('page_widget_id')
                  ->references('id')
                  ->on('page_widgets')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_testimonial_widgets');
    }
};
