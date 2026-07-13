<?php

namespace App\Models\Chatbot\Enterprise;

use App\Core\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentStatus extends BaseModel
{
    protected $table = 'chatbot_agent_statuses';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
