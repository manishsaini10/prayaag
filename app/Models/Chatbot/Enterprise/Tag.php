<?php

namespace App\Models\Chatbot\Enterprise;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Tag extends BaseModel
{
    protected $table = 'chatbot_tags';

    public function taggable(): MorphTo
    {
        return $this->morphTo();
    }
}
