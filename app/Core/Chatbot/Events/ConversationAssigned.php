<?php

namespace App\Core\Chatbot\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class ConversationAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(
        public string $conversationId,
        public string $agentId,
        public string $agentName
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('chatbot.conversation.' . $this->conversationId),
            new Channel('chatbot.agent.' . $this->agentId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'conversation.assigned';
    }

    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversationId,
            'agent_id' => $this->agentId,
            'agent_name' => $this->agentName,
            'assigned_at' => now()->toIso8601String(),
        ];
    }
}
