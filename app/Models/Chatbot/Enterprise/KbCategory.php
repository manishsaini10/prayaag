<?php

namespace App\Models\Chatbot\Enterprise;

use App\Core\Models\BaseModel;
use App\Models\Chatbot\ChatbotKbDocument;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KbCategory extends BaseModel
{
    protected $table = 'chatbot_kb_categories';

    public function parent(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(__CLASS__, 'parent_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ChatbotKbDocument::class, 'category_id');
    }
}
