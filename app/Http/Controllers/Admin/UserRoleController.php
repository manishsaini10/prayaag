<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserRoleController extends Controller
{
    public function index(): View
    {
        $users = User::with('roles')->orderBy('name')->paginate(20);
        return view('admin.user-roles-index', compact('users'));
    }

    public function edit(User $user): View
    {
        $roles = Role::withCount('permissions')->orderBy('name')->get();
        $userRoleIds = $user->roles->pluck('id')->toArray();

        return view('admin.user-roles', [
            'user'        => $user,
            'roles'       => $roles,
            'userRoleIds' => $userRoleIds,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $roleIds = array_keys($request->input('roles', []));
        $user->syncRoles(array_map('intval', $roleIds));

        return redirect()->route('admin.user-roles.edit', $user)
            ->with('status', 'Roles updated successfully.');
    }
}
