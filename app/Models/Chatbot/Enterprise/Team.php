<?php

namespace App\Models\Chatbot\Enterprise;

use App\Core\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends BaseModel
{
    protected $table = 'chatbot_teams';

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chatbot_team_member')
            ->withPivot('role');
    }
}
