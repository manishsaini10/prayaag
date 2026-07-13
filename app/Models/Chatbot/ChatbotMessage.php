<?php

namespace App\Models\Chatbot;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotMessage extends Model
{
    use HasUlids;

    protected $table = 'chatbot_messages';

    protected $guarded = ['id'];

    protected $casts = [
        'is_read' => 'boolean',
        'is_seen' => 'boolean',
        'metadata' => 'array',
        'file_size' => 'integer',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatbotConversation::class, 'conversation_id');
    }
}
