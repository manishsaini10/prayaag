<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PageLayout extends BaseModel
{
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class, 'layout_id');
    }
}
