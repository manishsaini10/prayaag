<?php

namespace App\Models\Chatbot\Enterprise;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends BaseModel
{
    protected $table = 'chatbot_languages';

    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class, 'language_id');
    }
}
