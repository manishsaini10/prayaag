<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Phase2Seeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            'pages.view', 'pages.create', 'pages.update', 'pages.delete',
            'posts.view', 'posts.create', 'posts.update', 'posts.delete',
            'users.view', 'users.create', 'users.update', 'users.delete',
            'settings.manage',
        ];

        $permissions = collect($catalog)->map(function (string $slug) {
            return Permission::firstOrCreate(
                ['name' => $slug, 'guard_name' => 'web'],
                ['name' => $slug, 'guard_name' => 'web']
            );
        });

        $superAdmin = Role::firstOrCreate(
            ['name' => 'super-admin', 'guard_name' => 'web'],
            ['name' => 'super-admin', 'guard_name' => 'web']
        );

        $editor = Role::firstOrCreate(
            ['name' => 'editor', 'guard_name' => 'web'],
            ['name' => 'editor', 'guard_name' => 'web']
        );

        $superAdmin->givePermissionTo($permissions);

        $editor->givePermissionTo(
            $permissions->filter(
                fn (Permission $p) => str_starts_with($p->name, 'pages.')
                    || str_starts_with($p->name, 'posts.')
            )
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@school.test'],
            ['name' => 'School Admin', 'password' => Hash::make('password')]
        );

        $admin->assignRole($superAdmin);
    }
}
