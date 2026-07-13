<?php

namespace App\Models\Chatbot\Enterprise;

use App\Core\Models\BaseModel;
use App\Models\Chatbot\ChatbotVisitor;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitorLocation extends BaseModel
{
    protected $table = 'chatbot_visitor_locations';

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(ChatbotVisitor::class, 'visitor_id');
    }
}
