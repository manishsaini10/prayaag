<?php

namespace App\Models\Chatbot\Enterprise;

use App\Core\Models\BaseModel;
use App\Models\Chatbot\ChatbotVisitor;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisitorSession extends BaseModel
{
    protected $table = 'chatbot_visitor_sessions';

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(ChatbotVisitor::class, 'visitor_id');
    }

    public function pages(): HasMany
    {
        return $this->hasMany(VisitorPage::class, 'session_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(VisitorEvent::class, 'session_id');
    }
}
