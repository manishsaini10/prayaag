<?php

namespace App\Core\Chatbot\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class AgentTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(
        public string $conversationId,
        public string $agentName = 'Agent'
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('chatbot.conversation.' . $this->conversationId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'agent.typing';
    }

    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversationId,
            'agent_name' => $this->agentName,
            'typing_at' => now()->toIso8601String(),
        ];
    }
}
