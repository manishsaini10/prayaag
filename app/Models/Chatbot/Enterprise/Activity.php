<?php

namespace App\Models\Chatbot\Enterprise;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends BaseModel
{
    protected $table = 'chatbot_activities';

    public function activitable(): MorphTo
    {
        return $this->morphTo();
    }
}
