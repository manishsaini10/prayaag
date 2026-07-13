<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ====================================================================
        // SECTION 1: DEPARTMENTS & TEAMS
        // ====================================================================

        // 1.1 DEPARTMENTS
        Schema::create('chatbot_departments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#6366f1');
            $table->string('icon')->nullable();
            $table->string('email')->nullable();
            $table->string('priority', 20)->default('medium');
            $table->boolean('is_active')->default(true);
            $table->json('business_hours')->nullable();
            $table->json('settings')->nullable();
            $table->integer('sort_order')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        // 1.2 DEPARTMENT AGENT PIVOT
        Schema::create('chatbot_department_agent', function (Blueprint $table) {
            $table->foreignUlid('department_id')->constrained('chatbot_departments')->cascadeOnDelete();
            $table->foreignUlid('agent_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_lead')->default(false);
            $table->integer('max_concurrent_chats')->default(5);
            $table->string('status', 20)->default('available');
            $table->primary(['department_id', 'agent_id']);
            $table->timestamps();
        });

        // 1.3 TEAMS
        Schema::create('chatbot_teams', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignUlid('lead_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        // 1.4 TEAM MEMBERS PIVOT
        Schema::create('chatbot_team_member', function (Blueprint $table) {
            $table->foreignUlid('team_id')->constrained('chatbot_teams')->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role', 20)->default('member');
            $table->primary(['team_id', 'user_id']);
            $table->timestamps();
        });

        // ====================================================================
        // SECTION 2: VISITOR TRACKING (ENHANCED)
        // ====================================================================

        // 2.1 VISITOR SESSIONS (enhanced from existing visitor)
        Schema::create('chatbot_visitor_sessions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('visitor_id')->constrained('chatbot_visitors')->cascadeOnDelete();
            $table->string('session_token', 64)->unique();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_seconds')->default(0);
            $table->integer('page_views')->default(0);
            $table->string('entry_page')->nullable();
            $table->string('exit_page')->nullable();
            $table->string('referrer')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('gclid')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['visitor_id', 'started_at']);
            $table->index('session_token');
        });

        // 2.2 VISITOR DEVICES
        Schema::create('chatbot_visitor_devices', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('visitor_id')->constrained('chatbot_visitors')->cascadeOnDelete();
            $table->string('device_type', 20)->nullable();
            $table->string('device_name')->nullable();
            $table->string('browser')->nullable();
            $table->string('browser_version')->nullable();
            $table->string('os')->nullable();
            $table->string('os_version')->nullable();
            $table->string('screen_resolution', 20)->nullable();
            $table->string('language', 10)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('timezone')->nullable();
            $table->timestamps();

            $table->index('visitor_id');
        });

        // 2.3 VISITOR LOCATIONS
        Schema::create('chatbot_visitor_locations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('visitor_id')->constrained('chatbot_visitors')->cascadeOnDelete();
            $table->string('country')->nullable();
            $table->string('country_code', 5)->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->string('zip', 20)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('isp')->nullable();
            $table->string('organization')->nullable();
            $table->timestamps();
        });

        // 2.4 VISITOR PAGE VIEWS
        Schema::create('chatbot_visitor_pages', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('visitor_id')->constrained('chatbot_visitors')->cascadeOnDelete();
            $table->foreignUlid('session_id')->constrained('chatbot_visitor_sessions')->cascadeOnDelete();
            $table->string('url');
            $table->string('title')->nullable();
            $table->string('referrer')->nullable();
            $table->integer('time_on_page')->default(0);
            $table->integer('scroll_depth')->default(0);
            $table->timestamp('visited_at')->nullable();
            $table->timestamps();

            $table->index(['visitor_id', 'visited_at']);
            $table->index('url');
        });

        // 2.5 VISITOR EVENTS
        Schema::create('chatbot_visitor_events', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('visitor_id')->constrained('chatbot_visitors')->cascadeOnDelete();
            $table->foreignUlid('session_id')->nullable()->constrained('chatbot_visitor_sessions')->nullOnDelete();
            $table->string('event_type', 50);
            $table->string('event_category', 50)->nullable();
            $table->string('event_label')->nullable();
            $table->text('event_value')->nullable();
            $table->string('page_url')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();

            $table->index(['visitor_id', 'event_type']);
            $table->index('occurred_at');
        });

        // ====================================================================
        // SECTION 3: CHAT ENHANCEMENTS
        // ====================================================================

        // 3.1 TYPING STATUS
        Schema::create('chatbot_typing_status', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('conversation_id')->constrained('chatbot_conversations')->cascadeOnDelete();
            $table->string('sender_type', 20);
            $table->string('sender_id', 36)->nullable();
            $table->boolean('is_typing')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index(['conversation_id', 'sender_type']);
        });

        // 3.2 READ RECEIPTS
        Schema::create('chatbot_read_receipts', function (Blueprint $table) {
            $table->foreignUlid('message_id')->constrained('chatbot_messages')->cascadeOnDelete();
            $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reader_type', 20);
            $table->timestamp('read_at');
            $table->primary(['message_id', 'user_id', 'reader_type']);
        });

        // 3.3 CONVERSATION TAGS
        Schema::create('chatbot_conversation_tags', function (Blueprint $table) {
            $table->foreignUlid('conversation_id')->constrained('chatbot_conversations')->cascadeOnDelete();
            $table->string('tag');
            $table->primary(['conversation_id', 'tag']);
            $table->timestamps();
        });

        // ====================================================================
        // SECTION 4: KNOWLEDGE BASE (ENHANCED)
        // ====================================================================

        // 4.1 KNOWLEDGE CATEGORIES
        Schema::create('chatbot_kb_categories', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->foreignUlid('parent_id')->nullable()->constrained('chatbot_kb_categories')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        // 4.2 ENHANCED KB DOCUMENTS (add new columns)
        Schema::table('chatbot_kb_documents', function (Blueprint $table) {
            $table->foreignUlid('category_id')->nullable()->constrained('chatbot_kb_categories')->nullOnDelete();
            $table->string('file_type', 20)->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->integer('word_count')->default(0);
            $table->integer('chunk_count')->default(0);
            $table->string('language', 10)->default('en');
            $table->string('author')->nullable();
            $table->string('version', 20)->nullable();
            $table->json('metadata')->nullable();
        });

        // 4.3 EMBEDDINGS (vector storage for RAG)
        Schema::create('chatbot_embeddings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('embeddingable_type');
            $table->string('embeddingable_id', 36);
            $table->text('content');
            $table->json('embedding_vector')->nullable();
            $table->string('model', 50)->nullable();
            $table->integer('dimensions')->default(0);
            $table->integer('token_count')->default(0);
            $table->timestamps();

            $table->index(['embeddingable_type', 'embeddingable_id']);
        });

        // ====================================================================
        // SECTION 5: TICKET SYSTEM
        // ====================================================================

        Schema::create('chatbot_tickets', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('ticket_number')->unique();
            $table->foreignUlid('conversation_id')->nullable()->constrained('chatbot_conversations')->nullOnDelete();
            $table->foreignUlid('visitor_id')->nullable()->constrained('chatbot_visitors')->nullOnDelete();
            $table->foreignUlid('contact_id')->nullable()->constrained('chatbot_contacts')->nullOnDelete();
            $table->string('subject');
            $table->text('description')->nullable();
            $table->string('status', 20)->default('open');
            $table->string('priority', 20)->default('medium');
            $table->string('category', 30)->nullable();
            $table->foreignUlid('department_id')->nullable()->constrained('chatbot_departments')->nullOnDelete();
            $table->foreignUlid('assigned_agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUlid('assigned_team_id')->nullable()->constrained('chatbot_teams')->nullOnDelete();
            $table->string('source', 30)->default('chatbot');
            $table->string('channel', 30)->default('web');
            $table->json('tags')->nullable();
            $table->json('custom_fields')->nullable();
            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->integer('response_time_seconds')->default(0);
            $table->integer('resolution_time_seconds')->default(0);
            $table->integer('sla_minutes')->nullable();
            $table->boolean('sla_breached')->default(false);
            $table->integer('satisfaction_rating')->nullable();
            $table->text('satisfaction_comment')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index('ticket_number');
            $table->index('assigned_agent_id');
        });

        Schema::create('chatbot_ticket_replies', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('ticket_id')->constrained('chatbot_tickets')->cascadeOnDelete();
            $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('replier_type', 20);
            $table->text('body');
            $table->json('attachments')->nullable();
            $table->boolean('is_internal')->default(false);
            $table->boolean('is_solution')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->index('ticket_id');
        });

        // ====================================================================
        // SECTION 6: CRM (CONTACTS, COMPANIES, PIPELINES)
        // ====================================================================

        // 6.1 CONTACTS
        Schema::create('chatbot_contacts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('visitor_id')->nullable()->constrained('chatbot_visitors')->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('alternative_phone')->nullable();
            $table->string('company')->nullable();
            $table->string('position')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('timezone')->nullable();
            $table->string('language', 10)->default('en');
            $table->string('source', 30)->default('chatbot');
            $table->string('status', 20)->default('lead');
            $table->integer('total_conversations')->default(0);
            $table->integer('total_tickets')->default(0);
            $table->timestamp('last_contacted_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->json('custom_fields')->nullable();
            $table->json('tags')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('email');
            $table->index('phone');
            $table->index('status');
        });

        // 6.2 COMPANIES
        Schema::create('chatbot_companies', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('domain')->nullable();
            $table->string('industry')->nullable();
            $table->string('size', 20)->nullable();
            $table->string('website')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->json('social_links')->nullable();
            $table->json('custom_fields')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('active');
            $table->softDeletes();
            $table->timestamps();
        });

        // 6.3 COMPANY CONTACT PIVOT
        Schema::create('chatbot_company_contact', function (Blueprint $table) {
            $table->foreignUlid('company_id')->constrained('chatbot_companies')->cascadeOnDelete();
            $table->foreignUlid('contact_id')->constrained('chatbot_contacts')->cascadeOnDelete();
            $table->string('role', 30)->nullable();
            $table->primary(['company_id', 'contact_id']);
            $table->timestamps();
        });

        // 6.4 PIPELINES
        Schema::create('chatbot_pipelines', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        // 6.5 PIPELINE STAGES
        Schema::create('chatbot_pipeline_stages', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('pipeline_id')->constrained('chatbot_pipelines')->cascadeOnDelete();
            $table->string('name');
            $table->string('color', 7)->default('#6366f1');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_win')->default(false);
            $table->boolean('is_lost')->default(false);
            $table->integer('probability')->default(0);
            $table->integer('days_to_close')->nullable();
            $table->timestamps();
        });

        // 6.6 DEALS
        Schema::create('chatbot_deals', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('title');
            $table->foreignUlid('contact_id')->constrained('chatbot_contacts')->cascadeOnDelete();
            $table->foreignUlid('company_id')->nullable()->constrained('chatbot_companies')->nullOnDelete();
            $table->foreignUlid('pipeline_id')->constrained('chatbot_pipelines')->cascadeOnDelete();
            $table->foreignUlid('stage_id')->constrained('chatbot_pipeline_stages')->cascadeOnDelete();
            $table->decimal('value', 12, 2)->default(0);
            $table->string('currency', 3)->default('INR');
            $table->foreignUlid('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('source', 30)->nullable();
            $table->date('expected_close_date')->nullable();
            $table->date('closed_date')->nullable();
            $table->integer('probability')->default(0);
            $table->string('status', 20)->default('open');
            $table->string('lost_reason')->nullable();
            $table->json('custom_fields')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['pipeline_id', 'stage_id']);
            $table->index('status');
        });

        // 6.7 LEAD SOURCES
        Schema::create('chatbot_lead_sources', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        // ====================================================================
        // SECTION 7: POLYMORPHIC TAGS, NOTES, ACTIVITIES
        // ====================================================================

        Schema::create('chatbot_tags', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color', 7)->default('#6366f1');
            $table->string('taggable_type');
            $table->string('taggable_id', 36);
            $table->timestamps();

            $table->index(['taggable_type', 'taggable_id']);
        });

        Schema::create('chatbot_notes', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('notable_type');
            $table->string('notable_id', 36);
            $table->text('body');
            $table->boolean('is_pinned')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['notable_type', 'notable_id']);
        });

        Schema::create('chatbot_activities', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_type', 20)->default('agent');
            $table->string('action');
            $table->string('description')->nullable();
            $table->string('activitable_type');
            $table->string('activitable_id', 36);
            $table->json('properties')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['activitable_type', 'activitable_id']);
            $table->index('action');
        });

        // ====================================================================
        // SECTION 8: CAMPAIGNS
        // ====================================================================

        Schema::create('chatbot_campaigns', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('type', 30);
            $table->string('channel', 30)->default('chat');
            $table->text('content')->nullable();
            $table->json('targeting_rules')->nullable();
            $table->json('schedule')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('status', 20)->default('draft');
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('sent_count')->default(0);
            $table->integer('delivered_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->integer('clicked_count')->default(0);
            $table->integer('converted_count')->default(0);
            $table->integer('bounced_count')->default(0);
            $table->integer('unsubscribed_count')->default(0);
            $table->json('analytics')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index('channel');
        });

        Schema::create('chatbot_campaign_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('campaign_id')->constrained('chatbot_campaigns')->cascadeOnDelete();
            $table->string('recipient_type', 20);
            $table->string('recipient_id', 36)->nullable();
            $table->string('recipient_email')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'status']);
        });

        // ====================================================================
        // SECTION 9: AUTOMATION BUILDER
        // ====================================================================

        Schema::create('chatbot_automations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('trigger_type', 50);
            $table->json('trigger_config')->nullable();
            $table->json('conditions')->nullable();
            $table->json('actions');
            $table->json('schedule')->nullable();
            $table->integer('priority')->default(0);
            $table->integer('max_executions')->default(0);
            $table->integer('execution_count')->default(0);
            $table->string('status', 20)->default('draft');
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_run_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('trigger_type');
            $table->index('status');
        });

        Schema::create('chatbot_automation_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('automation_id')->constrained('chatbot_automations')->cascadeOnDelete();
            $table->string('trigger_event', 50)->nullable();
            $table->json('context')->nullable();
            $table->boolean('conditions_met')->default(false);
            $table->json('executed_actions')->nullable();
            $table->string('status', 20)->default('completed');
            $table->text('error_message')->nullable();
            $table->integer('duration_ms')->default(0);
            $table->timestamps();

            $table->index(['automation_id', 'status']);
        });

        // ====================================================================
        // SECTION 10: WEBHOOKS
        // ====================================================================

        Schema::create('chatbot_webhooks', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('url');
            $table->string('secret')->nullable();
            $table->json('events');
            $table->string('method', 10)->default('POST');
            $table->json('headers')->nullable();
            $table->integer('retry_count')->default(3);
            $table->integer('timeout_seconds')->default(10);
            $table->string('status', 20)->default('active');
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('chatbot_webhook_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('webhook_id')->constrained('chatbot_webhooks')->cascadeOnDelete();
            $table->string('event');
            $table->json('payload')->nullable();
            $table->integer('response_status')->nullable();
            $table->text('response_body')->nullable();
            $table->integer('duration_ms')->default(0);
            $table->string('status', 20)->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['webhook_id', 'status']);
        });

        // ====================================================================
        // SECTION 11: NOTIFICATIONS
        // ====================================================================

        Schema::create('chatbot_notifications', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 50);
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('icon')->nullable();
            $table->string('action_url')->nullable();
            $table->string('notifiable_type')->nullable();
            $table->string('notifiable_id', 36)->nullable();
            $table->json('data')->nullable();
            $table->string('channel', 30)->default('in_app');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_read']);
            $table->index('type');
        });

        // 11.1 NOTIFICATION CHANNELS
        Schema::create('chatbot_notification_channels', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('channel', 30);
            $table->string('identifier')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('preferences')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'channel']);
        });

        // ====================================================================
        // SECTION 12: SETTINGS & CONFIG
        // ====================================================================

        // 12.1 INTEGRATION SETTINGS
        Schema::create('chatbot_integrations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('provider', 50);
            $table->string('type', 30);
            $table->json('credentials')->nullable();
            $table->json('config')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamp('last_sync_at')->nullable();
            $table->text('last_error')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['provider', 'type']);
        });

        // 12.2 API TOKENS
        Schema::create('chatbot_api_tokens', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->json('permissions')->nullable();
            $table->json('rate_limits')->nullable();
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_revoked')->default(false);
            $table->timestamps();
        });

        // 12.3 WIDGET THEMES
        Schema::create('chatbot_widget_themes', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('primary_color', 7)->default('#0b2545');
            $table->string('secondary_color', 7)->default('#134074');
            $table->string('text_color', 7)->default('#ffffff');
            $table->string('bg_color', 7)->default('#ffffff');
            $table->string('header_bg', 7)->default('#0b2545');
            $table->string('bubble_color', 7)->default('#6366f1');
            $table->string('font_family')->default('system-ui');
            $table->integer('border_radius')->default(12);
            $table->string('position', 30)->default('bottom-right');
            $table->boolean('is_dark')->default(false);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_rtl')->default(false);
            $table->text('custom_css')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        // 12.4 LANGUAGES
        Schema::create('chatbot_languages', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->string('native_name')->nullable();
            $table->boolean('is_rtl')->default(false);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('chatbot_translations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('key');
            $table->foreignUlid('language_id')->constrained('chatbot_languages')->cascadeOnDelete();
            $table->text('value');
            $table->string('group', 50)->default('widget');
            $table->timestamps();

            $table->unique(['key', 'language_id', 'group']);
        });

        // ====================================================================
        // SECTION 13: ANALYTICS & REPORTS
        // ====================================================================

        Schema::create('chatbot_analytics_events', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('event_type');
            $table->string('event_category', 50)->nullable();
            $table->string('session_id', 64)->nullable();
            $table->foreignUlid('visitor_id')->nullable()->constrained('chatbot_visitors')->nullOnDelete();
            $table->foreignUlid('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();

            $table->index(['event_type', 'occurred_at']);
            $table->index('visitor_id');
        });

        Schema::create('chatbot_reports', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('type', 30);
            $table->json('config');
            $table->json('data')->nullable();
            $table->string('schedule', 30)->nullable();
            $table->json('recipients')->nullable();
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_generated_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // ====================================================================
        // SECTION 14: AGENT PERFORMANCE & STATUS
        // ====================================================================

        Schema::create('chatbot_agent_statuses', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 20)->default('offline');
            $table->string('away_reason')->nullable();
            $table->integer('active_chats')->default(0);
            $table->integer('max_chats')->default(5);
            $table->timestamp('available_at')->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->timestamp('logged_in_at')->nullable();
            $table->timestamp('logged_out_at')->nullable();
            $table->timestamps();
        });

        Schema::create('chatbot_agent_performance', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('date');
            $table->integer('conversations_count')->default(0);
            $table->integer('tickets_resolved')->default(0);
            $table->integer('messages_sent')->default(0);
            $table->integer('avg_response_time_seconds')->default(0);
            $table->integer('avg_resolution_time_seconds')->default(0);
            $table->decimal('satisfaction_score', 3, 2)->default(0);
            $table->integer('chats_handoff')->default(0);
            $table->integer('missed_chats')->default(0);
            $table->integer('total_online_seconds')->default(0);
            $table->json('metrics')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'date']);
        });

        // ====================================================================
        // SECTION 15: AUDIT LOG
        // ====================================================================

        Schema::create('chatbot_audit_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('resource_type');
            $table->string('resource_id', 36)->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['resource_type', 'resource_id']);
            $table->index('action');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_audit_logs');
        Schema::dropIfExists('chatbot_agent_performance');
        Schema::dropIfExists('chatbot_agent_statuses');
        Schema::dropIfExists('chatbot_reports');
        Schema::dropIfExists('chatbot_analytics_events');
        Schema::dropIfExists('chatbot_translations');
        Schema::dropIfExists('chatbot_languages');
        Schema::dropIfExists('chatbot_widget_themes');
        Schema::dropIfExists('chatbot_api_tokens');
        Schema::dropIfExists('chatbot_integrations');
        Schema::dropIfExists('chatbot_notification_channels');
        Schema::dropIfExists('chatbot_notifications');
        Schema::dropIfExists('chatbot_webhook_logs');
        Schema::dropIfExists('chatbot_webhooks');
        Schema::dropIfExists('chatbot_automation_logs');
        Schema::dropIfExists('chatbot_automations');
        Schema::dropIfExists('chatbot_campaign_logs');
        Schema::dropIfExists('chatbot_campaigns');
        Schema::dropIfExists('chatbot_activities');
        Schema::dropIfExists('chatbot_notes');
        Schema::dropIfExists('chatbot_tags');
        Schema::dropIfExists('chatbot_lead_sources');
        Schema::dropIfExists('chatbot_deals');
        Schema::dropIfExists('chatbot_pipeline_stages');
        Schema::dropIfExists('chatbot_pipelines');
        Schema::dropIfExists('chatbot_company_contact');
        Schema::dropIfExists('chatbot_companies');
        Schema::dropIfExists('chatbot_contacts');
        Schema::dropIfExists('chatbot_ticket_replies');
        Schema::dropIfExists('chatbot_tickets');
        Schema::dropIfExists('chatbot_embeddings');
        Schema::dropIfExists('chatbot_kb_categories');
        Schema::dropIfExists('chatbot_conversation_tags');
        Schema::dropIfExists('chatbot_read_receipts');
        Schema::dropIfExists('chatbot_typing_status');
        Schema::dropIfExists('chatbot_visitor_events');
        Schema::dropIfExists('chatbot_visitor_pages');
        Schema::dropIfExists('chatbot_visitor_locations');
        Schema::dropIfExists('chatbot_visitor_devices');
        Schema::dropIfExists('chatbot_visitor_sessions');
        Schema::dropIfExists('chatbot_team_member');
        Schema::dropIfExists('chatbot_teams');
        Schema::dropIfExists('chatbot_department_agent');
        Schema::dropIfExists('chatbot_departments');

        // Revert kb_documents schema changes
        Schema::table('chatbot_kb_documents', function (Blueprint $table) {
            $table->dropColumn([
                'category_id', 'file_type', 'file_size', 'word_count',
                'chunk_count', 'language', 'author', 'version', 'metadata'
            ]);
        });
    }
};
