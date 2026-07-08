<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Bootstraps the site: the global permission catalog, two roles
 * (super-admin, editor), and an admin user.
 * Run: php artisan db:seed --class=Database\\Seeders\\Phase2Seeder
 */
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
            [$group] = explode('.', $slug);

            return Permission::firstOrCreate(
                ['slug' => $slug],
                ['name' => Str::headline(str_replace('.', ' ', $slug)), 'group' => $group]
            );
        });

        $superAdmin = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            ['name' => 'Super Admin']
        );

        $editor = Role::firstOrCreate(
            ['slug' => 'editor'],
            ['name' => 'Editor']
        );

        $superAdmin->permissions()->syncWithoutDetaching($permissions->pluck('id'));

        $editor->permissions()->syncWithoutDetaching(
            $permissions->filter(
                fn (Permission $p) => str_starts_with($p->slug, 'pages.')
                    || str_starts_with($p->slug, 'posts.')
            )->pluck('id')
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@school.test'],
            ['name' => 'School Admin', 'password' => Hash::make('password')]
        );

        $admin->assignRole($superAdmin);
    }
}
