<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase 3 — Database Schema & Data Integrity Migration.
     *
     * 1. Foreign Key Constraints:
     *    - video_testimonials.reviewed_by -> users(id) ON DELETE SET NULL
     *    - data_privacy_requests.processed_by -> users(id) ON DELETE SET NULL
     *    - mess_menus.created_by -> users(id) ON DELETE SET NULL
     *
     * 2. Composite & Covered Indexes:
     *    - enquiries (status, type, created_at)
     *    - job_applications (job_listing_id, status, created_at)
     *    - job_listings (status, closes_at)
     *    - form_submissions (form_id, created_at)
     *    - video_testimonials (status, is_featured, sort_order)
     */
    public function up(): void
    {
        // ── 1. Foreign Key Constraints ────────────────────────────────────────

        if (Schema::hasTable('video_testimonials') && Schema::hasColumn('video_testimonials', 'reviewed_by')) {
            Schema::table('video_testimonials', function (Blueprint $table) {
                $table->foreign('reviewed_by')
                      ->references('id')
                      ->on('users')
                      ->nullOnDelete();
            });
        }

        if (Schema::hasTable('data_privacy_requests') && Schema::hasColumn('data_privacy_requests', 'processed_by')) {
            Schema::table('data_privacy_requests', function (Blueprint $table) {
                $table->foreign('processed_by')
                      ->references('id')
                      ->on('users')
                      ->nullOnDelete();
            });
        }

        if (Schema::hasTable('mess_menus') && Schema::hasColumn('mess_menus', 'created_by')) {
            Schema::table('mess_menus', function (Blueprint $table) {
                $table->foreign('created_by')
                      ->references('id')
                      ->on('users')
                      ->nullOnDelete();
            });
        }

        // ── 2. Composite & Covered Performance Indexes ───────────────────────

        if (Schema::hasTable('enquiries')) {
            Schema::table('enquiries', function (Blueprint $table) {
                $table->index(['status', 'type', 'created_at'], 'enquiries_status_type_created_idx');
            });
        }

        if (Schema::hasTable('job_applications')) {
            Schema::table('job_applications', function (Blueprint $table) {
                $table->index(['job_listing_id', 'status', 'created_at'], 'job_apps_listing_status_created_idx');
            });
        }

        if (Schema::hasTable('job_listings')) {
            Schema::table('job_listings', function (Blueprint $table) {
                $table->index(['status', 'closes_at'], 'job_listings_status_closes_idx');
            });
        }

        if (Schema::hasTable('form_submissions')) {
            Schema::table('form_submissions', function (Blueprint $table) {
                $table->index(['form_id', 'created_at'], 'form_subs_form_created_idx');
            });
        }

        if (Schema::hasTable('video_testimonials')) {
            Schema::table('video_testimonials', function (Blueprint $table) {
                $table->index(['status', 'is_featured', 'sort_order'], 'video_test_status_feat_sort_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('video_testimonials')) {
            Schema::table('video_testimonials', function (Blueprint $table) {
                $table->dropForeign(['reviewed_by']);
                $table->dropIndex('video_test_status_feat_sort_idx');
            });
        }

        if (Schema::hasTable('data_privacy_requests')) {
            Schema::table('data_privacy_requests', function (Blueprint $table) {
                $table->dropForeign(['processed_by']);
            });
        }

        if (Schema::hasTable('mess_menus')) {
            Schema::table('mess_menus', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
            });
        }

        if (Schema::hasTable('enquiries')) {
            Schema::table('enquiries', function (Blueprint $table) {
                $table->dropIndex('enquiries_status_type_created_idx');
            });
        }

        if (Schema::hasTable('job_applications')) {
            Schema::table('job_applications', function (Blueprint $table) {
                $table->dropIndex('job_apps_listing_status_created_idx');
            });
        }

        if (Schema::hasTable('job_listings')) {
            Schema::table('job_listings', function (Blueprint $table) {
                $table->dropIndex('job_listings_status_closes_idx');
            });
        }

        if (Schema::hasTable('form_submissions')) {
            Schema::table('form_submissions', function (Blueprint $table) {
                $table->dropIndex('form_subs_form_created_idx');
            });
        }
    }
};
