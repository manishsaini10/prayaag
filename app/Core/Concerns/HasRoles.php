<?php

namespace App\Core\Concerns;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * RBAC for the User model. Roles are site-wide; permissions are a
 * global catalog assigned to roles. A user's effective permissions are
 * the union of its roles' permissions.
 */
trait HasRoles
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function assignRole(Role|string $role): void
    {
        $role = $role instanceof Role ? $role : Role::where('slug', $role)->firstOrFail();
        $this->roles()->syncWithoutDetaching([$role->id]);
        $this->unsetRelation('roles');
    }

    public function hasRole(string $slug): bool
    {
        return $this->roles->contains(fn (Role $role) => $role->slug === $slug);
    }

    public function permissions(): Collection
    {
        return $this->roles->loadMissing('permissions')
            ->flatMap->permissions
            ->unique('id')
            ->values();
    }

    public function hasPermission(string $slug): bool
    {
        return $this->permissions()->contains(fn (Permission $permission) => $permission->slug === $slug);
    }
}
