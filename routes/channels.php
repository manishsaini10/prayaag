<?php

use App\Models\Chatbot\ChatbotConversation;
use App\Models\Chatbot\ChatbotVisitor;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chatbot.conversation.{conversationId}', function ($user, $conversationId) {
    if ($user) {
        return ['id' => $user->id, 'name' => $user->name, 'type' => 'agent'];
    }
    return false;
});

Broadcast::channel('chatbot.agent.{agentId}', function ($user, $agentId) {
    return (int) $user->id === (int) $agentId;
});
