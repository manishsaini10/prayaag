<?php

namespace App\Models\Chatbot;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatbotKbDocument extends BaseModel
{
    protected $table = 'chatbot_kb_documents';

    protected $casts = [
        'is_active' => 'boolean',
        'indexed_at' => 'datetime',
    ];

    public function chunks(): HasMany
    {
        return $this->hasMany(ChatbotKbChunk::class, 'document_id');
    }
}
