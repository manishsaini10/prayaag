<?php

namespace App\Models\Chatbot;

use App\Core\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotLead extends BaseModel
{
    protected $table = 'chatbot_leads';

    protected $casts = [
        'follow_up_date' => 'date',
    ];

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(ChatbotVisitor::class, 'visitor_id');
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }
}
