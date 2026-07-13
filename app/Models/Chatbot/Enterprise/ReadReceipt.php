<?php

namespace App\Models\Chatbot\Enterprise;

use App\Core\Models\BaseModel;
use App\Models\Chatbot\ChatbotMessage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadReceipt extends BaseModel
{
    protected $table = 'chatbot_read_receipts';

    public function message(): BelongsTo
    {
        return $this->belongsTo(ChatbotMessage::class, 'message_id');
    }
}
