<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Global ability catalog (e.g. pages.create). Roles decide who is granted them.
 */
class Permission extends BaseModel
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
}
