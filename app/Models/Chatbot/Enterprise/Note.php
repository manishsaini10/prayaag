<?php

namespace App\Models\Chatbot\Enterprise;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Note extends BaseModel
{
    protected $table = 'chatbot_notes';

    public function notable(): MorphTo
    {
        return $this->morphTo();
    }
}
