<?php

namespace App\Models\Chatbot\Enterprise;

use App\Core\Models\BaseModel;
use App\Models\Chatbot\ChatbotConversation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationTag extends BaseModel
{
    protected $table = 'chatbot_conversation_tags';

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatbotConversation::class, 'conversation_id');
    }
}
