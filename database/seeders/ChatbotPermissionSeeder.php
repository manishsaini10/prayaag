<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class ChatbotPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            ...$this->group('chatbot.departments', 'Departments', 'view', 'create', 'update', 'delete', 'assign'),
            ...$this->group('chatbot.tickets', 'Tickets', 'view', 'create', 'update', 'delete', 'reply', 'assign', 'status'),
            ...$this->group('chatbot.campaigns', 'Campaigns', 'view', 'create', 'update', 'delete', 'send', 'duplicate'),
            ...$this->group('chatbot.automations', 'Automations', 'view', 'create', 'update', 'delete', 'toggle', 'test'),
            ...$this->group('chatbot.analytics', 'Analytics', 'view', 'reports'),
            ...$this->group('chatbot.contacts', 'Contacts', 'view', 'create', 'update', 'delete'),
            ...$this->group('chatbot.kb', 'Knowledge Base', 'view', 'upload', 'delete', 'index-cms'),
            ...$this->group('chatbot.webhooks', 'Webhooks', 'view', 'create', 'update', 'delete', 'test'),
            ...$this->group('chatbot.settings', 'Settings', 'view', 'update'),
            ...$this->group('chatbot.conversations', 'Conversations', 'view', 'assign', 'close'),
        ];

        $permissions = collect($catalog)->map(function (string $slug) {
            return Permission::firstOrCreate(
                ['name' => $slug, 'guard_name' => 'web'],
                ['name' => $slug, 'guard_name' => 'web']
            );
        });

        $superAdmin = Role::where('name', 'super-admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }
    }

    private function group(string $prefix, string $group, string ...$actions): array
    {
        return array_map(fn (string $action) => "$prefix.$action", $actions);
    }
}
