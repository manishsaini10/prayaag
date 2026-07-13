<?php

namespace App\Models\Chatbot\Enterprise;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookLog extends BaseModel
{
    protected $table = 'chatbot_webhook_logs';

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class, 'webhook_id');
    }
}
