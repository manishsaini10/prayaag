<?php

namespace App\Models\Chatbot\Enterprise;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Automation extends BaseModel
{
    protected $table = 'chatbot_automations';

    protected $fillable = [
        'name',
        'description',
        'trigger_type',
        'trigger_config',
        'conditions',
        'actions',
        'schedule',
        'priority',
        'max_executions',
        'execution_count',
        'is_active',
        'status',
        'created_by',
        'last_run_at',
    ];

    protected $casts = [
        'trigger_config' => 'json',
        'conditions' => 'json',
        'actions' => 'json',
        'schedule' => 'json',
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
    ];

    public const TRIGGERS = [
        'ticket_created',
        'ticket_updated',
        'ticket_status_changed',
        'ticket_priority_changed',
        'deal_created',
        'deal_moved',
        'deal_stage_changed',
        'deal_won',
        'deal_lost',
        'visitor_identified',
        'visitor_online',
        'conversation_started',
        'conversation_ended',
        'canned_response_used',
        'sla_breached',
        'sla_at_risk',
        'schedule',
        'webhook_received',
    ];

    public const ACTIONS = [
        'send_email',
        'send_notification',
        'create_ticket',
        'update_ticket',
        'assign_ticket',
        'move_deal',
        'update_deal',
        'send_webhook',
        'send_sms',
        'add_tag',
        'log_message',
    ];

    public const STATUSES = ['draft', 'active', 'paused', 'completed', 'disabled'];

    public function logs(): HasMany
    {
        return $this->hasMany(AutomationLog::class, 'automation_id');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->where('status', 'active');
    }

    public function triggerLabel(): string
    {
        return str_replace('_', ' ', ucfirst($this->trigger_type));
    }

    public function canExecute(): bool
    {
        if (!$this->is_active || $this->status !== 'active') return false;
        if ($this->max_executions > 0 && $this->execution_count >= $this->max_executions) return false;
        return true;
    }
}
