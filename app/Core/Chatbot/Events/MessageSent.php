<?php

namespace App\Core\Chatbot\Events;

use App\Models\Chatbot\ChatbotMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(
        public ChatbotMessage $message
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('chatbot.conversation.' . $this->message->conversation_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_type' => $this->message->sender_type,
            'message_text' => $this->message->message_text,
            'message_type' => $this->message->message_type,
            'metadata' => $this->message->metadata,
            'created_at' => $this->message->created_at,
        ];
    }
}
