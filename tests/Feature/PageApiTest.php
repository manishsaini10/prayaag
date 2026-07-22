<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PageApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        $this->admin = User::create(['name' => 'Admin', 'email' => 'admin@demo.test', 'password' => Hash::make('x')]);
        $this->admin->assignRole($role);
    }

    public function test_admin_can_list_pages(): void
    {
        Page::create(['title' => 'Home', 'slug' => 'home', 'status' => 'published']);

        $this->actingAs($this->admin)
            ->getJson('/api/pages')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_user_without_permission_cannot_create_a_page(): void
    {
        $plain = User::create(['name' => 'Plain', 'email' => 'plain@demo.test', 'password' => Hash::make('x')]);

        $this->actingAs($plain)
            ->postJson('/api/pages', ['title' => 'X', 'slug' => 'x'])
            ->assertForbidden();
    }

    public function test_admin_can_sync_a_full_page_tree(): void
    {
        $page = Page::create(['title' => 'Home', 'slug' => 'home', 'status' => 'published']);

        $payload = [
            'sections' => [[
                'type' => 'hero',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type'     => 'heading',
                            'settings' => ['text' => 'Hi', 'level' => 1],
                        ]],
                    ]],
                ]],
            ]],
        ];

        $this->actingAs($this->admin)
            ->putJson("/api/pages/{$page->id}/tree", $payload)
            ->assertOk()
            ->assertJsonPath('success', true);

        $page->refresh()->load('sections.rows.columns.widgets');
        $widget = $page->sections->first()->rows->first()->columns->first()->widgets->first();

        $this->assertSame(1, $page->sections->count());
        $this->assertSame('heading', $widget->widget_type);
        $this->assertSame('Hi', $widget->settings['text']);
    }

    public function test_syncing_a_tree_replaces_the_previous_one(): void
    {
        $page = Page::create(['title' => 'Home', 'slug' => 'home', 'status' => 'published']);

        $tree = ['sections' => [['type' => 'hero', 'rows' => [['columns' => [['width' => 12, 'widgets' => []]]]]]]];

        $this->actingAs($this->admin)->putJson("/api/pages/{$page->id}/tree", $tree)->assertOk();
        $this->actingAs($this->admin)->putJson("/api/pages/{$page->id}/tree", $tree)->assertOk();

        // Two syncs must not accumulate sections.
        $this->assertSame(1, $page->sections()->count());
    }
}
