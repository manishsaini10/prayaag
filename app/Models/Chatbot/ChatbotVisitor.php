<?php

namespace App\Models\Chatbot;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Chatbot\Enterprise\VisitorSession;

class ChatbotVisitor extends Model
{
    use HasUlids;

    protected $table = 'chatbot_visitors';

    protected $guarded = ['id'];

    public function conversations(): HasMany
    {
        return $this->hasMany(ChatbotConversation::class, 'visitor_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(ChatbotLead::class, 'visitor_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(VisitorSession::class, 'visitor_id');
    }
}
