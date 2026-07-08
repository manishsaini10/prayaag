<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_inherits_permissions_from_its_roles(): void
    {
        $perm = Permission::create(['name' => 'Create pages', 'slug' => 'pages.create', 'group' => 'pages']);
        $role = Role::create(['name' => 'Editor', 'slug' => 'editor']);
        $role->permissions()->attach($perm->id);

        $user = User::create(['name' => 'E', 'email' => 'e@a.test', 'password' => Hash::make('x')]);
        $user->assignRole($role);

        $this->assertTrue($user->fresh()->hasRole('editor'));
        $this->assertTrue($user->fresh()->hasPermission('pages.create'));
        $this->assertFalse($user->fresh()->hasPermission('pages.delete'));
    }

    public function test_super_admin_passes_every_gate(): void
    {
        $role = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);
        $user = User::create(['name' => 'S', 'email' => 's@a.test', 'password' => Hash::make('x')]);
        $user->assignRole($role);

        $this->assertTrue($user->fresh()->can('anything.at.all'));
    }

    public function test_user_without_permission_is_denied(): void
    {
        $user = User::create(['name' => 'N', 'email' => 'n@a.test', 'password' => Hash::make('x')]);

        $this->assertFalse($user->can('pages.delete'));
    }
}
