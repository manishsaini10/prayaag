<?php

namespace Tests\Feature\Chatbot;

use App\Models\Chatbot\ChatbotSetting;
use App\Models\Chatbot\ChatbotVisitor;
use App\Models\Chatbot\ChatbotConversation;
use App\Models\Chatbot\ChatbotMessage;
use App\Models\Chatbot\ChatbotLead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatbotFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_get_chatbot_config(): void
    {
        $response = $this->getJson(route('chatbot.widget.config'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['enable_chatbot', 'primary_color']);
    }

    public function test_it_can_initialize_session_for_visitor(): void
    {
        $response = $this->postJson(route('chatbot.widget.init'), [
            'session_id' => 'test_session_123',
            'landing_page' => '/',
            'referrer' => 'google.com'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['session_id', 'visitor', 'conversation_id', 'messages']);
        
        $this->assertDatabaseHas('chatbot_visitors', [
            'session_id' => 'test_session_123',
            'referrer' => 'google.com'
        ]);
    }

    public function test_it_can_create_lead_from_prechat_form(): void
    {
        $visitor = ChatbotVisitor::create([
            'session_id' => 'test_lead_session',
            'name' => 'Old Name',
        ]);

        $response = $this->postJson(route('chatbot.widget.lead'), [
            'session_id' => 'test_lead_session',
            'name' => 'Alok Sharma',
            'email' => 'alok@example.com',
            'phone' => '9876543210',
            'class' => 'primary',
            'interest' => 'Admission Info'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        $this->assertDatabaseHas('chatbot_leads', [
            'name' => 'Alok Sharma',
            'email' => 'alok@example.com',
            'admission_class' => 'primary',
            'source' => 'chatbot',
        ]);
    }

    public function test_admin_can_update_chatbot_settings(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)
            ->post(route('admin.chatbot.settings.update'), [
                'enable_chatbot' => '1',
                'enable_ai' => '1',
                'primary_color' => '#123456',
                'widget_position' => 'bottom-left',
                'widget_shape' => 'rounded',
                'launcher_style' => 'icon',
                'settings_data' => [
                    'ai' => [
                        'provider' => 'gemini',
                        'model' => 'gemini-1.5-flash',
                        'api_key' => 'test_key',
                        'temperature' => 0.5,
                        'max_tokens' => 500,
                    ]
                ]
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('chatbot_settings', [
            'enable_chatbot' => true,
            'primary_color' => '#123456',
            'widget_position' => 'bottom-left'
        ]);
    }

    public function test_admin_can_send_message_to_visitor(): void
    {
        $admin = User::factory()->create();
        $visitor = ChatbotVisitor::create([
            'session_id' => 'test_sess_456',
            'name' => 'Visitor Name',
        ]);
        $convo = ChatbotConversation::create([
            'visitor_id' => $visitor->id,
            'status' => 'open',
            'priority' => 'medium',
        ]);

        $response = $this->actingAs($admin)
            ->postJson(route('admin.chatbot.conversations.send', ['id' => $convo->id]), [
                'message' => 'Hello from administrator'
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'conversation_id', 'sender_type', 'message_text']);

        $this->assertDatabaseHas('chatbot_messages', [
            'conversation_id' => $convo->id,
            'sender_type' => 'agent',
            'message_text' => 'Hello from administrator',
        ]);
    }

    public function test_it_routes_to_live_agent_when_ai_offline(): void
    {
        // 1. Set settings to enable live agent but empty AI key
        $settings = ChatbotSetting::first() ?? ChatbotSetting::create();
        $settings->update([
            'enable_chatbot' => true,
            'enable_live_agent' => true,
            'settings_data' => [
                'ai' => [
                    'provider' => 'gemini',
                    'model' => 'gemini-1.5-flash',
                    'api_key' => '', // Offline!
                ]
            ]
        ]);

        $visitor = ChatbotVisitor::create([
            'session_id' => 'offline_test_sess',
            'name' => 'Offline Visitor',
        ]);

        $convo = ChatbotConversation::create([
            'visitor_id' => $visitor->id,
            'status' => 'open',
            'ai_handled' => true,
        ]);

        $response = $this->postJson(route('chatbot.widget.send'), [
            'conversation_id' => $convo->id,
            'message' => 'Hello there, is anyone there?'
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message_text' => 'Connecting you to an online support representative. Please wait...'
        ]);

        // Check if ai_handled was toggled off so admin takes over
        $convo->refresh();
        $this->assertFalse((bool) $convo->ai_handled);
    }

    public function test_admin_can_assign_conversation_to_agent(): void
    {
        $admin = User::factory()->create();
        $agent = User::factory()->create();
        $visitor = ChatbotVisitor::create([
            'session_id' => 'assign_test_sess',
            'name' => 'Assignee Visitor',
        ]);
        $convo = ChatbotConversation::create([
            'visitor_id' => $visitor->id,
            'status' => 'open',
            'ai_handled' => true,
        ]);

        $response = $this->actingAs($admin)
            ->postJson(route('admin.chatbot.conversations.assign', ['id' => $convo->id]), [
                'operator_id' => $agent->id,
            ]);

        $response->assertStatus(200);
        $convo->refresh();
        $this->assertEquals($agent->id, $convo->operator_id);
        $this->assertFalse((bool) $convo->ai_handled); // Disables AI responses
    }

    public function test_embed_script_serves_valid_javascript(): void
    {
        $response = $this->get(route('chatbot.embed.js'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/javascript');
        $response->assertHeader('Access-Control-Allow-Origin', '*');
        $content = $response->getContent();
        $this->assertStringContainsString('Prayaag Help Desk', $content);
        $this->assertStringContainsString('BASE_URL', $content);
        $this->assertStringContainsString('chatbot-widget-container', $content);
    }

    public function test_visitor_can_close_chat_session(): void
    {
        $visitor = ChatbotVisitor::create([
            'session_id' => 'close_test_sess',
            'name' => 'Close Visitor',
        ]);
        $convo = ChatbotConversation::create([
            'visitor_id' => $visitor->id,
            'status' => 'open',
        ]);

        $response = $this->postJson(route('chatbot.widget.close', ['id' => $convo->id]));

        $response->assertStatus(200);
        $convo->refresh();
        $this->assertEquals('closed', $convo->status);
    }
}
