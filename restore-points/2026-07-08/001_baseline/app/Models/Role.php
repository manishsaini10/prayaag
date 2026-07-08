<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Role. Defines a named set of permissions drawn from the global catalog.
 */
class Role extends BaseModel
{
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function givePermission(Permission|string $permission): void
    {
        $permission = $permission instanceof Permission
            ? $permission
            : Permission::where('slug', $permission)->firstOrFail();

        $this->permissions()->syncWithoutDetaching([$permission->id]);
    }
}
