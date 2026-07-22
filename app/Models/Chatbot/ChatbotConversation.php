<?php

namespace App\Models\Chatbot;

use App\Core\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatbotConversation extends BaseModel
{
    protected $table = 'chatbot_conversations';

    protected $casts = [
        'tags'            => 'array',
        'meta'            => 'array',
        'ai_handled'      => 'boolean',
        'cost'            => 'decimal:4',
        'rating'          => 'integer',
        'response_time'   => 'integer',
        'resolution_time' => 'integer',
    ];

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(ChatbotVisitor::class, 'visitor_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatbotMessage::class, 'conversation_id');
    }
}
