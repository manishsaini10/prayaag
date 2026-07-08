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
        if ($user->hasRole('Super Admin')) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('popup.view');
    }

    public function view(User $user, Popup $popup): bool
    {
        return $user->hasPermission('popup.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('popup.create');
    }

    public function update(User $user, Popup $popup): bool
    {
        return $user->hasPermission('popup.update');
    }

    public function delete(User $user, Popup $popup): bool
    {
        return $user->hasPermission('popup.delete');
    }

    public function restore(User $user, Popup $popup): bool
    {
        return $user->hasPermission('popup.restore');
    }

    public function forceDelete(User $user, Popup $popup): bool
    {
        return $user->hasPermission('popup.force-delete');
    }

    public function publish(User $user, Popup $popup): bool
    {
        return $user->hasPermission('popup.publish');
    }

    public function duplicate(User $user, Popup $popup): bool
    {
        return $user->hasPermission('popup.duplicate');
    }

    public function manageLeads(User $user, Popup $popup): bool
    {
        return $user->hasPermission('popup.manage-leads');
    }

    public function viewAnalytics(User $user, Popup $popup): bool
    {
        return $user->hasPermission('popup.analytics');
    }
}
