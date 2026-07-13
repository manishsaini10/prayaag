<?php

namespace App\Models\Chatbot\Enterprise;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Translation extends BaseModel
{
    protected $table = 'chatbot_translations';

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
