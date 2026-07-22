<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. POPUP CATEGORIES
        Schema::create('popup_categories', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#6366f1');
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('parent_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // 2. POPUP TEMPLATES
        Schema::create('popup_templates', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->string('type')->default('modal');
            $table->string('category')->nullable();
            $table->string('thumbnail')->nullable();
            $table->json('structure');          // full popup structure
            $table->json('settings')->nullable();
            $table->json('styles')->nullable();
            $table->boolean('is_premium')->default(false);
            $table->boolean('is_built_in')->default(true);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        // 3. A/B TESTS (Moved up to prevent foreign key errors in popups)
        Schema::create('popup_ab_tests', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('status', 20)->default('draft');
            $table->string('goal_type', 30)->default('click'); // click, conversion, form_submit
            $table->string('winner_determination', 30)->default('conversion_rate');
            $table->decimal('min_confidence', 5, 2)->default(95.00);
            $table->integer('min_sample_size')->default(100);
            $table->integer('traffic_split')->default(50);      // % for original/variant A
            $table->boolean('auto_winner')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->string('winner_id')->nullable();
            $table->json('results')->nullable();                // aggregated results
            $table->softDeletes();
            $table->timestamps();
        });

        // 4. POPUPS (main table)
        Schema::create('popups', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('type', 50)->default('modal');
            $table->string('status', 20)->default('draft');
            $table->foreignUlid('category_id')->nullable()->constrained('popup_categories')->nullOnDelete();
            $table->foreignUlid('template_id')->nullable()->constrained('popup_templates')->nullOnDelete();

            // Content (full JSON structure for the drag-drop builder)
            $table->json('structure');
            $table->json('settings')->nullable();     // general popup settings
            $table->json('design')->nullable();        // design overrides
            $table->json('styles')->nullable();         // CSS customizations
            $table->text('custom_css')->nullable();
            $table->text('custom_js')->nullable();

            // Scheduling
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('use_recurring_schedule')->default(false);
            $table->json('recurring_schedule')->nullable(); // {"days":["mon","tue"],"time_start":"09:00","time_end":"18:00","timezone":"Asia/Kolkata"}

            // Frequency
            $table->string('frequency_type', 30)->default('once_per_session');
            $table->integer('frequency_delay')->default(0);   // seconds
            $table->integer('frequency_x_days')->nullable();   // for after_x_days
            $table->integer('max_views_per_user')->nullable();

            // A/B Testing
            $table->boolean('is_ab_test')->default(false);
            $table->foreignUlid('ab_test_id')->nullable()->constrained('popup_ab_tests')->nullOnDelete();

            // Stats (cached counters)
            $table->bigInteger('view_count')->default(0);
            $table->bigInteger('impression_count')->default(0);
            $table->bigInteger('click_count')->default(0);
            $table->bigInteger('conversion_count')->default(0);

            // Priority / ordering
            $table->integer('priority')->default(0);
            $table->bigInteger('sort_order')->default(0);

            // Metadata
            $table->json('meta')->nullable();       // seo, og, twitter cards
            $table->boolean('noindex')->default(false);
            $table->boolean('is_template')->default(false);

            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUlid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index(['status', 'starts_at', 'ends_at']);
            $table->index('type');
            $table->index('priority');
        });

        // 5. A/B TEST VARIANTS (Moved up to prevent foreign key errors in popup_analytics)
        Schema::create('popup_ab_test_variants', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('ab_test_id')->constrained('popup_ab_tests')->cascadeOnDelete();
            $table->string('name');
            $table->string('variant_type', 10)->default('variant'); // original, variant
            $table->json('structure');
            $table->json('settings')->nullable();
            $table->json('design')->nullable();
            $table->bigInteger('view_count')->default(0);
            $table->bigInteger('conversion_count')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        // 6. POPUP RULES (polymorphic - trigger, display, targeting, frequency)
        Schema::create('popup_rules', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('popup_id')->constrained('popups')->cascadeOnDelete();
            $table->string('type', 20);              // trigger, display, targeting, frequency
            $table->string('rule_key');               // e.g. page_load, exit_intent, country, etc.
            $table->string('condition', 20)->default('is');  // is, is_not, contains, greater_than, less_than, between, regex
            $table->text('value')->nullable();         // single value or JSON array
            $table->json('extra')->nullable();          // additional parameters
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['popup_id', 'type']);
            $table->index('rule_key');
        });

        // 7. POPUP SCHEDULES
        Schema::create('popup_schedules', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('popup_id')->constrained('popups')->cascadeOnDelete();
            $table->string('type', 20);               // date_range, recurring, business_hours, holiday
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->json('schedule_data')->nullable();  // flexible schedule data
            $table->string('timezone', 50)->default('UTC');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 8. POPUP ANALYTICS (Referenced tables are now created first)
        Schema::create('popup_analytics', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('popup_id')->constrained('popups')->cascadeOnDelete();
            $table->foreignUlid('variation_id')->nullable()->constrained('popup_ab_test_variants')->nullOnDelete();
            $table->string('event_type', 30);          // view, impression, click, conversion, close
            $table->string('session_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->string('referrer')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('device_type', 10)->nullable(); // desktop, tablet, mobile
            $table->string('browser', 30)->nullable();
            $table->string('os', 30)->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->json('extra_data')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['popup_id', 'event_type'], 'popup_analytics_event_idx');
            $table->index('occurred_at', 'popup_analytics_occurred_idx');
        });

        // 9. POPUP LEADS
        Schema::create('popup_leads', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('popup_id')->constrained('popups')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->json('form_data')->nullable();       // all submitted form fields
            $table->string('status', 20)->default('new');
            $table->text('notes')->nullable();
            $table->string('source')->nullable();         // popup_url, referrer, etc.
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('country', 2)->nullable();
            $table->json('tags')->nullable();
            $table->foreignUlid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('converted_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['popup_id', 'status']);
            $table->index('email');
            $table->index('phone');
        });

        // 10. POPUP INTEGRATIONS
        Schema::create('popup_integrations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('provider', 30);               // mailchimp, slack, webhook, etc.
            $table->string('type', 20);                   // source, destination, webhook
            $table->json('credentials')->nullable();       // encrypted API keys
            $table->json('config')->nullable();            // provider-specific settings
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_synced_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // 11. POPUP INTEGRATION LOGS
        Schema::create('popup_integration_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('integration_id')->nullable()->constrained('popup_integrations')->nullOnDelete();
            $table->foreignUlid('popup_id')->nullable()->constrained('popups')->nullOnDelete();
            $table->string('event_type', 30);
            $table->string('status', 20);                  // success, failed, pending
            $table->text('request')->nullable();
            $table->text('response')->nullable();
            $table->integer('status_code')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        // 12. POPUP ASSETS
        Schema::create('popup_assets', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('popup_id')->nullable()->constrained('popups')->nullOnDelete();
            $table->string('name');
            $table->string('file_name');
            $table->string('path');
            $table->string('disk', 20)->default('public');
            $table->string('mime_type', 100);
            $table->bigInteger('size')->default(0);
            $table->string('type', 30);                    // image, video, font, custom_css, custom_js
            $table->json('metadata')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // 13. POPUP REVISIONS
        Schema::create('popup_revisions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('popup_id')->constrained('popups')->cascadeOnDelete();
            $table->integer('version');
            $table->string('note')->nullable();
            $table->json('structure');
            $table->json('settings')->nullable();
            $table->json('design')->nullable();
            $table->json('diff')->nullable();               // changes from previous version
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['popup_id', 'version']);
            $table->index('created_at');
        });

        // 14. POPUP ACTIVITY LOGS
        Schema::create('popup_activity_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('popup_id')->nullable()->constrained('popups')->nullOnDelete();
            $table->string('action', 50);                  // created, updated, published, paused, trashed, restored, etc.
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->foreignUlid('causer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['popup_id', 'action']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        $tables = [
            'popup_activity_logs', 'popup_revisions', 'popup_assets',
            'popup_integration_logs', 'popup_integrations', 'popup_leads',
            'popup_analytics', 'popup_ab_test_variants', 'popup_schedules', 
            'popup_rules', 'popups', 'popup_ab_tests',
            'popup_templates', 'popup_categories',
        ];
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
