<?php

namespace App\Models\Chatbot\Enterprise;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationLog extends BaseModel
{
    protected $table = 'chatbot_automation_logs';

    protected $fillable = [
        'automation_id',
        'trigger_event',
        'context',
        'conditions_met',
        'executed_actions',
        'status',
        'error_message',
        'duration_ms',
    ];

    protected $casts = [
        'context' => 'json',
        'executed_actions' => 'json',
        'conditions_met' => 'boolean',
    ];

    public function automation(): BelongsTo
    {
        return $this->belongsTo(Automation::class, 'automation_id');
    }
}
