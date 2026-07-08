<?php

namespace Tests\Feature;

use App\Core\Builder\PageRenderer;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditorTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create(['name' => 'Admin', 'email' => 'admin@a.test', 'password' => 'password']);
    }

    public function test_editor_requires_authentication(): void
    {
        $page = Page::create(['title' => 'Home', 'slug' => 'home', 'status' => 'published']);

        $this->get('/admin/pages/' . $page->id . '/edit')->assertRedirect('/login');
    }

    public function test_tree_endpoint_returns_the_structure(): void
    {
        $page = Page::create(['title' => 'Home', 'slug' => 'home', 'status' => 'published']);
        $section = $page->sections()->create(['section_type' => 'section', 'sort_order' => 0]);
        $row = $section->rows()->create(['sort_order' => 0]);
        $column = $row->columns()->create(['width' => 12, 'sort_order' => 0]);
        $column->widgets()->create(['widget_type' => 'heading', 'sort_order' => 0, 'settings' => ['text' => 'Hi', 'level' => 1]]);

        $this->actingAs($this->user)
            ->getJson('/admin/pages/' . $page->id . '/tree')
            ->assertOk()
            ->assertJsonPath('sections.0.rows.0.columns.0.widgets.0.type', 'heading');
    }

    public function test_save_replaces_the_tree_and_renders(): void
    {
        $page = Page::create(['title' => 'Home', 'slug' => 'home', 'status' => 'published']);

        $payload = ['sections' => [[
            'type' => 'section',
            'rows' => [[
                'columns' => [[
                    'width'   => 12,
                    'widgets' => [[
                        'type'     => 'heading',
                        'settings' => ['text' => 'Hello Editor', 'level' => 2],
                    ]],
                ]],
            ]],
        ]]];

        $this->actingAs($this->user)
            ->putJson('/admin/pages/' . $page->id . '/tree', $payload)
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertDatabaseHas('page_widgets', ['widget_type' => 'heading']);

        $html = app(PageRenderer::class)->render($page->fresh());
        $this->assertStringContainsString('Hello Editor', $html);
    }

    public function test_preview_renders_an_unsaved_tree(): void
    {
        $page = Page::create(['title' => 'Home', 'slug' => 'home', 'status' => 'published']);

        $payload = ['sections' => [[
            'type' => 'section',
            'rows' => [[
                'columns' => [[
                    'width'   => 12,
                    'widgets' => [[
                        'type'     => 'heading',
                        'settings' => ['text' => 'Preview Heading', 'level' => 2],
                    ]],
                ]],
            ]],
        ]]];

        $this->actingAs($this->user)
            ->postJson('/admin/pages/' . $page->id . '/preview', $payload)
            ->assertOk()
            ->assertJsonPath('html', fn ($html) => str_contains((string) $html, 'Preview Heading'));

        // Preview must not persist anything.
        $this->assertSame(0, $page->sections()->count());
    }
}
