<?php

namespace App\Core\Chatbot\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class VisitorTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(
        public string $conversationId,
        public string $visitorName = 'Visitor'
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('chatbot.conversation.' . $this->conversationId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'visitor.typing';
    }

    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversationId,
            'visitor_name' => $this->visitorName,
            'typing_at' => now()->toIso8601String(),
        ];
    }
}
