<?php

namespace App\Core\Chatbot\Repositories;

use App\Models\Chatbot\ChatbotSetting;
use App\Models\Chatbot\ChatbotVisitor;
use App\Models\Chatbot\ChatbotConversation;
use App\Models\Chatbot\ChatbotMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ChatbotRepository
{
    public function __construct(
        private readonly ChatbotSetting $settingModel,
        private readonly ChatbotVisitor $visitorModel,
        private readonly ChatbotConversation $conversationModel,
        private readonly ChatbotMessage $messageModel
    ) {}

    public function getSettings(): ChatbotSetting
    {
        return Cache::remember('chatbot:settings', 86400, function () {
            return $this->settingModel->firstOrCreate([], [
                'enable_chatbot' => true,
                'enable_ai' => true,
                'enable_live_agent' => true,
                'enable_offline_form' => true,
                'widget_position' => 'bottom-right',
                'widget_shape' => 'rounded',
                'launcher_style' => 'icon',
                'primary_color' => '#0b2545',
                'secondary_color' => '#134074',
            ]);
        });
    }

    public function updateSettings(array $data): ChatbotSetting
    {
        $settings = $this->getSettings();
        $settings->update($data);
        Cache::forget('chatbot:settings');
        return $settings->fresh();
    }

    public function findOrCreateVisitor(string $sessionId, array $data = []): ChatbotVisitor
    {
        return $this->visitorModel->firstOrCreate(
            ['session_id' => $sessionId],
            $data
        );
    }

    public function findConversationById(string $id): ?ChatbotConversation
    {
        return $this->conversationModel->with(['visitor', 'operator'])->find($id);
    }

    public function getActiveConversations(): Collection
    {
        return $this->conversationModel->with(['visitor', 'operator'])
            ->whereIn('status', ['open', 'pending'])
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function createConversation(string $visitorId, array $data = []): ChatbotConversation
    {
        return $this->conversationModel->create(array_merge([
            'visitor_id' => $visitorId,
            'status' => 'open',
            'priority' => 'medium',
            'ai_handled' => true,
        ], $data));
    }

    public function createMessage(string $conversationId, string $senderType, ?string $senderId, ?string $text, string $type = 'text', array $extra = []): ChatbotMessage
    {
        $message = $this->messageModel->create(array_merge([
            'conversation_id' => $conversationId,
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'message_text' => $text,
            'message_type' => $type,
            'is_read' => false,
            'is_seen' => false,
        ], $extra));

        $conversation = $this->findConversationById($conversationId);
        if ($conversation) {
            $conversation->touch();
        }

        return $message;
    }

    public function getMessages(string $conversationId): Collection
    {
        return $this->messageModel->where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
