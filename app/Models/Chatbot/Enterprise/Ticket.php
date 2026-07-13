<?php

namespace App\Models\Chatbot\Enterprise;

use App\Core\Models\BaseModel;
use App\Models\Chatbot\ChatbotConversation;
use App\Models\Chatbot\ChatbotVisitor;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends BaseModel
{
    protected $table = 'chatbot_tickets';

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::creating(function (self $ticket) {
            $ticket->ticket_number = 'TKT-' . strtoupper(uniqid());
        });
    }

    public function scopePriorityQueue($query, string $queue = 'all')
    {
        $order = match ($queue) {
            'critical' => $query->where('priority', 'critical'),
            'urgent' => $query->where('priority', 'urgent'),
            'high' => $query->where('priority', 'high'),
            'medium' => $query->where('priority', 'medium'),
            'low' => $query->where('priority', 'low'),
            default => $query,
        };

        return $query->orderByRaw("FIELD(priority, 'critical','urgent','high','medium','low')")
            ->orderBy('created_at');
    }

    public function scopeSlaBreached($query)
    {
        return $query->where('sla_breached', true)
            ->whereIn('status', ['open', 'pending']);
    }

    public function scopeSlaAtRisk($query)
    {
        return $query->where('sla_breached', false)
            ->whereNotNull('sla_minutes')
            ->whereIn('status', ['open', 'pending'])
            ->whereRaw('TIMESTAMPDIFF(SECOND, created_at, NOW()) > (sla_minutes * 60 * 0.8)');
    }

    public function checkSla(): bool
    {
        if (!$this->sla_minutes || $this->status === 'resolved' || $this->status === 'closed') {
            return false;
        }

        $elapsed = now()->diffInSeconds($this->created_at);
        $threshold = $this->sla_minutes * 60;

        if ($elapsed > $threshold && !$this->sla_breached) {
            $this->update(['sla_breached' => true]);
            return true;
        }

        return false;
    }

    public function markFirstResponse(): void
    {
        if (!$this->first_response_at) {
            $this->update([
                'first_response_at' => now(),
                'response_time_seconds' => (int) $this->created_at->diffInSeconds(now()),
            ]);
        }
    }

    public function resolve(): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolution_time_seconds' => (int) $this->created_at->diffInSeconds(now()),
        ]);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatbotConversation::class, 'conversation_id');
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(ChatbotVisitor::class, 'visitor_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class, 'ticket_id');
    }
}
