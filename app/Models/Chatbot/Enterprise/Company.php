<?php

namespace App\Models\Chatbot\Enterprise;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Company extends BaseModel
{
    protected $table = 'chatbot_companies';

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'chatbot_company_contact');
    }
}
