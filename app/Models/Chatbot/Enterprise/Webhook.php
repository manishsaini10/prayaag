<?php

namespace App\Models\Chatbot\Enterprise;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Webhook extends BaseModel
{
    protected $table = 'chatbot_webhooks';

    public function logs(): HasMany
    {
        return $this->hasMany(WebhookLog::class, 'webhook_id');
    }
}
