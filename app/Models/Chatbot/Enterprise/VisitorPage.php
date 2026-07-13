<?php

namespace App\Models\Chatbot\Enterprise;

use App\Core\Models\BaseModel;
use App\Models\Chatbot\ChatbotVisitor;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitorPage extends BaseModel
{
    protected $table = 'chatbot_visitor_pages';

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(ChatbotVisitor::class, 'visitor_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(VisitorSession::class, 'session_id');
    }
}
