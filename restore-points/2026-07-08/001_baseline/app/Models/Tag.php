<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends BaseModel
{
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }
}
