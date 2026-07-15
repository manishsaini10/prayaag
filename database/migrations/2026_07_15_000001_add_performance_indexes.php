<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_views', function (Blueprint $table) {
            $table->index(['path', 'viewed_at'], 'page_views_path_viewed_idx');
            $table->index(['page_id', 'viewed_at'], 'page_views_page_viewed_idx');
            $table->index(['ip_hash', 'viewed_at'], 'page_views_ip_viewed_idx');
        });

        Schema::table('not_found_logs', function (Blueprint $table) {
            $table->index(['resolved', 'last_seen_at'], 'not_found_resolved_seen_idx');
        });

        Schema::table('popups', function (Blueprint $table) {
            $table->index(['status', 'priority', 'starts_at', 'ends_at'], 'popups_live_priority_idx');
        });

        Schema::table('popup_rules', function (Blueprint $table) {
            $table->index(['type', 'rule_key', 'is_active'], 'popup_rules_lookup_idx');
        });

        Schema::table('popup_analytics', function (Blueprint $table) {
            $table->index(['popup_id', 'event_type', 'occurred_at'], 'popup_analytics_popup_event_time_idx');
            $table->index(['popup_id', 'device_type', 'occurred_at'], 'popup_analytics_popup_device_time_idx');
            $table->index(['session_id', 'occurred_at'], 'popup_analytics_session_time_idx');
        });

        Schema::table('chatbot_conversations', function (Blueprint $table) {
            $table->index(['operator_id', 'status', 'updated_at'], 'chatbot_conv_operator_status_updated_idx');
            $table->index(['visitor_id', 'created_at'], 'chatbot_conv_visitor_created_idx');
        });

        Schema::table('chatbot_messages', function (Blueprint $table) {
            $table->index(['conversation_id', 'created_at'], 'chatbot_messages_conv_created_idx');
            $table->index(['conversation_id', 'is_read', 'created_at'], 'chatbot_messages_conv_read_created_idx');
        });

        Schema::table('chatbot_leads', function (Blueprint $table) {
            $table->index(['status', 'follow_up_date'], 'chatbot_leads_status_followup_idx');
            $table->index(['assigned_agent_id', 'status'], 'chatbot_leads_agent_status_idx');
        });

        Schema::table('chatbot_analytics_events', function (Blueprint $table) {
            $table->index(['visitor_id', 'event_type', 'occurred_at'], 'chatbot_events_visitor_event_time_idx');
            $table->index(['agent_id', 'event_type', 'occurred_at'], 'chatbot_events_agent_event_time_idx');
        });
    }

    public function down(): void
    {
        Schema::table('chatbot_analytics_events', function (Blueprint $table) {
            $table->dropIndex('chatbot_events_agent_event_time_idx');
            $table->dropIndex('chatbot_events_visitor_event_time_idx');
        });

        Schema::table('chatbot_leads', function (Blueprint $table) {
            $table->dropIndex('chatbot_leads_agent_status_idx');
            $table->dropIndex('chatbot_leads_status_followup_idx');
        });

        Schema::table('chatbot_messages', function (Blueprint $table) {
            $table->dropIndex('chatbot_messages_conv_read_created_idx');
            $table->dropIndex('chatbot_messages_conv_created_idx');
        });

        Schema::table('chatbot_conversations', function (Blueprint $table) {
            $table->dropIndex('chatbot_conv_visitor_created_idx');
            $table->dropIndex('chatbot_conv_operator_status_updated_idx');
        });

        Schema::table('popup_analytics', function (Blueprint $table) {
            $table->dropIndex('popup_analytics_session_time_idx');
            $table->dropIndex('popup_analytics_popup_device_time_idx');
            $table->dropIndex('popup_analytics_popup_event_time_idx');
        });

        Schema::table('popup_rules', function (Blueprint $table) {
            $table->dropIndex('popup_rules_lookup_idx');
        });

        Schema::table('popups', function (Blueprint $table) {
            $table->dropIndex('popups_live_priority_idx');
        });

        Schema::table('not_found_logs', function (Blueprint $table) {
            $table->dropIndex('not_found_resolved_seen_idx');
        });

        Schema::table('page_views', function (Blueprint $table) {
            $table->dropIndex('page_views_ip_viewed_idx');
            $table->dropIndex('page_views_page_viewed_idx');
            $table->dropIndex('page_views_path_viewed_idx');
        });
    }
};
