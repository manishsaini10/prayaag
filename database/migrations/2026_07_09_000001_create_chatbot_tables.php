<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. CHATBOT SETTINGS
        Schema::create('chatbot_settings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->boolean('enable_chatbot')->default(true);
            $table->boolean('enable_ai')->default(true);
            $table->boolean('enable_live_agent')->default(true);
            $table->boolean('enable_offline_form')->default(true);
            $table->boolean('enable_email_notifications')->default(false);
            $table->boolean('enable_whatsapp_fallback')->default(false);
            $table->boolean('enable_kb')->default(true);
            $table->boolean('enable_visitor_tracking')->default(true);
            $table->boolean('enable_sound_notification')->default(true);
            $table->boolean('enable_typing_indicator')->default(true);
            $table->boolean('enable_read_receipts')->default(true);
            $table->boolean('enable_seen_status')->default(true);
            $table->boolean('enable_conversation_rating')->default(true);
            $table->boolean('enable_file_upload')->default(false);
            $table->boolean('enable_voice_messages')->default(false);
            $table->boolean('enable_emoji')->default(true);
            $table->boolean('enable_dark_mode')->default(false);
            $table->boolean('enable_custom_css')->default(false);
            $table->boolean('enable_custom_js')->default(false);
            $table->boolean('enable_chat_history')->default(true);
            $table->boolean('enable_departments')->default(false);
            $table->boolean('enable_auto_assignment')->default(false);
            $table->boolean('enable_business_hours')->default(false);

            // Widget Styling & Placement
            $table->string('widget_position', 30)->default('bottom-right');
            $table->string('widget_shape', 30)->default('rounded');
            $table->string('launcher_style', 30)->default('icon');
            $table->string('primary_color', 7)->default('#0b2545');
            $table->string('secondary_color', 7)->default('#134074');
            $table->text('custom_css')->nullable();
            $table->text('custom_js')->nullable();
            $table->json('settings_data')->nullable(); // Working Days, hours config, WhatsApp number, fallback criteria, API key, Model info
            $table->timestamps();
        });

        // 2. CHATBOT VISITORS
        Schema::create('chatbot_visitors', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('session_id')->unique();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('device', 50)->nullable();
            $table->string('browser', 50)->nullable();
            $table->string('os', 50)->nullable();
            $table->string('landing_page')->nullable();
            $table->string('referrer')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('current_page')->nullable();
            $table->timestamps();

            $table->index('session_id');
        });

        // 3. CHATBOT CONVERSATIONS
        Schema::create('chatbot_conversations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('visitor_id')->constrained('chatbot_visitors')->cascadeOnDelete();
            $table->string('status', 20)->default('open'); // open, closed, transfer, pending
            $table->string('priority', 20)->default('medium'); // low, medium, high
            $table->foreignUlid('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('department')->nullable();
            $table->integer('rating')->nullable();
            $table->text('notes')->nullable();
            $table->json('tags')->nullable();
            $table->integer('token_usage')->default(0);
            $table->decimal('cost', 8, 4)->default(0.0000);
            $table->integer('response_time')->nullable(); // seconds
            $table->integer('resolution_time')->nullable(); // seconds
            $table->boolean('ai_handled')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'priority']);
        });

        // 4. CHATBOT MESSAGES
        Schema::create('chatbot_messages', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('conversation_id')->constrained('chatbot_conversations')->cascadeOnDelete();
            $table->string('sender_type', 20); // visitor, agent, chatbot
            $table->string('sender_id', 36)->nullable(); // User ULID or Visitor ULID
            $table->text('message_text')->nullable();
            $table->string('message_type', 20)->default('text'); // text, file, quick_reply, rating
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('is_seen')->default(false);
            $table->json('metadata')->nullable(); // options, quick replies
            $table->timestamps();

            $table->index('conversation_id');
        });

        // 5. CHATBOT KB DOCUMENTS
        Schema::create('chatbot_kb_documents', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('title');
            $table->string('type', 20); // faq, page, blog, file
            $table->string('source_id', 36)->nullable();
            $table->string('file_path')->nullable();
            $table->longText('content');
            $table->boolean('is_active')->default(true);
            $table->timestamp('indexed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('type');
        });

        // 6. CHATBOT KB CHUNKS
        Schema::create('chatbot_kb_chunks', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('document_id')->constrained('chatbot_kb_documents')->cascadeOnDelete();
            $table->text('chunk_text');
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        // 7. CHATBOT LEADS
        Schema::create('chatbot_leads', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('visitor_id')->nullable()->constrained('chatbot_visitors')->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('interest')->nullable();
            $table->string('admission_class')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('new'); // new, qualified, lost, contacted
            $table->string('source', 30)->default('chatbot');
            $table->foreignUlid('assigned_agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('follow_up_date')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
        });

        // 8. CHATBOT FLOWS
        Schema::create('chatbot_flows', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->json('flow_data');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_flows');
        Schema::dropIfExists('chatbot_leads');
        Schema::dropIfExists('chatbot_kb_chunks');
        Schema::dropIfExists('chatbot_kb_documents');
        Schema::dropIfExists('chatbot_messages');
        Schema::dropIfExists('chatbot_conversations');
        Schema::dropIfExists('chatbot_visitors');
        Schema::dropIfExists('chatbot_settings');
    }
};
