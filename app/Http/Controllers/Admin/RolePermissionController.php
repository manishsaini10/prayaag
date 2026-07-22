<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RolePermissionController extends Controller
{
    public function index(): View
    {
        $roles = Role::withCount('permissions')->orderBy('name')->get();
        return view('admin.role-permissions-index', compact('roles'));
    }

    public function edit(Role $role): View
    {
        $permissions = Permission::orderBy('name')->get();
        $rolePermIds = $role->permissions->pluck('id')->toArray();

        $groups = $permissions->groupBy(function (Permission $p) {
            $parts = explode('.', $p->name);
            return count($parts) >= 3 ? $parts[0] . '/' . $parts[1] : $parts[0];
        });

        return view('admin.role-permissions', [
            'role'        => $role,
            'groups'      => $groups,
            'rolePermIds' => $rolePermIds,
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $permIds = array_keys($request->input('permissions', []));
        $role->syncPermissions(array_map('intval', $permIds));

        return redirect()->route('admin.role-permissions.edit', $role)
            ->with('status', 'Permissions updated successfully.');
    }
}
