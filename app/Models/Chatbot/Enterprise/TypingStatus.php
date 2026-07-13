<?php

namespace App\Models\Chatbot\Enterprise;

use App\Core\Models\BaseModel;
use App\Models\Chatbot\ChatbotConversation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TypingStatus extends BaseModel
{
    protected $table = 'chatbot_typing_status';

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatbotConversation::class, 'conversation_id');
    }
}
