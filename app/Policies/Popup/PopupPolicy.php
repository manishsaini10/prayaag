<?php

namespace App\Policies\Popup;

use App\Models\Popup\Popup;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PopupPolicy
{
    use HandlesAuthorization;

    public function before(User $user): ?bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('popup.view');
    }

    public function view(User $user, Popup $popup): bool
    {
        return $user->hasPermissionTo('popup.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('popup.create');
    }

    public function update(User $user, Popup $popup): bool
    {
        return $user->hasPermissionTo('popup.update');
    }

    public function delete(User $user, Popup $popup): bool
    {
        return $user->hasPermissionTo('popup.delete');
    }

    public function restore(User $user, Popup $popup): bool
    {
        return $user->hasPermissionTo('popup.restore');
    }

    public function forceDelete(User $user, Popup $popup): bool
    {
        return $user->hasPermissionTo('popup.force-delete');
    }

    public function publish(User $user, Popup $popup): bool
    {
        return $user->hasPermissionTo('popup.publish');
    }

    public function duplicate(User $user, Popup $popup): bool
    {
        return $user->hasPermissionTo('popup.duplicate');
    }

    public function manageLeads(User $user, Popup $popup): bool
    {
        return $user->hasPermissionTo('popup.manage-leads');
    }

    public function viewAnalytics(User $user, Popup $popup): bool
    {
        return $user->hasPermissionTo('popup.analytics');
    }
}
