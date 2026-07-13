<?php

namespace App\Models\Chatbot;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotKbChunk extends Model
{
    use HasUlids;

    protected $table = 'chatbot_kb_chunks';

    protected $guarded = ['id'];

    protected $casts = [
        'meta' => 'array',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(ChatbotKbDocument::class, 'document_id');
    }
}
