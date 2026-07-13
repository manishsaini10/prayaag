<?php

namespace App\Models\Chatbot\Enterprise;

use App\Core\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Department extends BaseModel
{
    protected $table = 'chatbot_departments';

    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chatbot_department_agent')
            ->withPivot('is_lead', 'max_concurrent_chats', 'status');
    }
}
