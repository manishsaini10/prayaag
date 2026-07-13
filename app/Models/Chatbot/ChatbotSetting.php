<?php

namespace App\Models\Chatbot;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class ChatbotSetting extends Model
{
    use HasUlids;

    protected $table = 'chatbot_settings';

    protected $guarded = ['id'];

    protected $casts = [
        'enable_chatbot' => 'boolean',
        'enable_ai' => 'boolean',
        'enable_live_agent' => 'boolean',
        'enable_offline_form' => 'boolean',
        'enable_email_notifications' => 'boolean',
        'enable_whatsapp_fallback' => 'boolean',
        'enable_kb' => 'boolean',
        'enable_visitor_tracking' => 'boolean',
        'enable_sound_notification' => 'boolean',
        'enable_typing_indicator' => 'boolean',
        'enable_read_receipts' => 'boolean',
        'enable_seen_status' => 'boolean',
        'enable_conversation_rating' => 'boolean',
        'enable_file_upload' => 'boolean',
        'enable_voice_messages' => 'boolean',
        'enable_emoji' => 'boolean',
        'enable_dark_mode' => 'boolean',
        'enable_custom_css' => 'boolean',
        'enable_custom_js' => 'boolean',
        'enable_chat_history' => 'boolean',
        'enable_departments' => 'boolean',
        'enable_auto_assignment' => 'boolean',
        'enable_business_hours' => 'boolean',
        'settings_data' => 'array',
    ];
}
