<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Daily summary table
        Schema::create('analytics_daily_summary', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->date('date')->unique();
            $table->unsignedInteger('total_views')->default(0);
            $table->unsignedInteger('unique_visitors')->default(0);
            $table->unsignedInteger('new_visitors')->default(0);
            $table->unsignedInteger('returning_visitors')->default(0);
            $table->unsignedInteger('avg_session_duration')->default(0);
            $table->decimal('bounce_rate', 5, 2)->default(0);
            $table->unsignedInteger('total_leads')->default(0);
            $table->unsignedInteger('total_chatbot_conversations')->default(0);
            $table->timestamps();
        });

        // 2. Page summary table
        Schema::create('analytics_page_summary', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->date('date');
            $table->string('page_id', 26)->nullable();
            $table->string('url', 500);
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('unique_views')->default(0);
            $table->unsignedInteger('avg_time_on_page')->default(0);
            $table->timestamps();

            $table->index('date');
            $table->index('page_id');
        });

        // 3. Traffic source summary table
        Schema::create('analytics_source_summary', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->date('date');
            $table->string('source', 255)->default('direct');
            $table->string('medium', 255)->nullable();
            $table->string('campaign', 255)->nullable();
            $table->unsignedInteger('visits')->default(0);
            $table->unsignedInteger('leads_generated')->default(0);
            $table->timestamps();

            $table->index('date');
        });

        // 4. Custom widgets table
        Schema::create('analytics_dashboard_widgets', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('user_id', 26);
            $table->string('widget_key', 100);
            $table->unsignedInteger('position')->default(0);
            $table->json('settings')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_dashboard_widgets');
        Schema::dropIfExists('analytics_source_summary');
        Schema::dropIfExists('analytics_page_summary');
        Schema::dropIfExists('analytics_daily_summary');
    }
};
