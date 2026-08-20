<?php

namespace App\Http\Controllers\Admin;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Core\Builder\WidgetRegistry;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Backs the visual page-editor MVP. Data moves over session-authenticated web
 * routes (cookie + CSRF), reusing PageTreeService for the atomic save so the
 * editor and the public renderer share one source of truth.
 */
class EditorController extends Controller
{
    /** Simple page picker so the editor is reachable from the dashboard. */
    public function index(): View
    {
        $pages = Page::orderBy('title')->get();

        return view('admin.pages-index', ['pages' => $pages]);
    }

    /** Create a new page and initialize an initial layout container. */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:pages,slug',
            'status'           => 'required|in:published,draft',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $slug = !empty($data['slug']) 
            ? \Illuminate\Support\Str::slug($data['slug']) 
            : \Illuminate\Support\Str::slug($data['title']);

        // Double-check slug uniqueness
        $originalSlug = $slug;
        $count = 1;
        while (Page::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        $page = new Page();
        $page->title  = $data['title'];
        $page->slug   = $slug;
        $page->status = $data['status'];
        $page->seo    = [
            'meta_title'       => $data['meta_title'] ?? $data['title'],
            'meta_description' => $data['meta_description'] ?? null,
        ];
        $page->save();

        // Create initial section, row, and column for builder
        $section = \App\Models\PageSection::create([
            'page_id'      => $page->id,
            'section_type' => 'container',
            'sort_order'   => 0,
            'settings'     => [],
        ]);

        $row = \App\Models\PageRow::create([
            'section_id' => $section->id,
            'sort_order' => 0,
            'settings'   => [],
        ]);

        \App\Models\PageColumn::create([
            'row_id'     => $row->id,
            'width'      => 12,
            'sort_order' => 0,
            'settings'   => [],
        ]);

        return redirect()->route('admin.pages.edit', $page)
            ->with('status', "Page '{$page->title}' created! Design your layout using the Visual Page Builder.");
    }

    /** Update basic page metadata (Title, Slug, Status, SEO). */
    public function updateMeta(Page $page, Request $request, PageRenderer $renderer): RedirectResponse
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'slug'             => 'required|string|max:255|unique:pages,slug,' . $page->id,
            'status'           => 'required|in:published,draft',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $page->title  = $data['title'];
        $page->slug   = \Illuminate\Support\Str::slug($data['slug']);
        $page->status = $data['status'];
        $page->seo    = [
            'meta_title'       => $data['meta_title'] ?? $data['title'],
            'meta_description' => $data['meta_description'] ?? null,
        ];
        $page->save();

        $renderer->forget($page);

        return redirect()->route('admin.pages.index')
            ->with('status', "Page '{$page->title}' updated successfully.");
    }

    /** Delete a page and cascade-remove all child sections/widgets. */
    public function destroy(Page $page, PageRenderer $renderer): RedirectResponse
    {
        if (in_array($page->slug, ['home', 'index'])) {
            return redirect()->route('admin.pages.index')
                ->with('error', 'The home page is core to the website and cannot be deleted.');
        }

        $title = $page->title;

        // Cascade delete child sections, rows, columns, and widgets
        foreach ($page->sections as $section) {
            foreach ($section->rows as $row) {
                foreach ($row->columns as $column) {
                    $column->widgets()->delete();
                }
                $row->columns()->delete();
            }
            $section->rows()->delete();
        }
        $page->sections()->delete();

        $renderer->forget($page);
        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('status', "Page '{$title}' has been deleted successfully.");
    }

    /** "Page Builder" entry point: jump straight into the home page's builder. */
    public function builder(): RedirectResponse
    {
        $page = Page::where('slug', 'home')->first() ?? Page::orderBy('title')->first();

        if (! $page) {
            return redirect()->route('admin.pages.index')
                ->with('status', 'No pages exist yet — create one first, then open the builder.');
        }

        return redirect()->route('admin.pages.edit', $page);
    }

    /** The editor shell + the widget palette. */
    public function edit(Page $page, WidgetRegistry $registry): View
    {
        $palette = [];
        foreach ($registry->all() as $widget) {
            $palette[] = [
                'type'     => $widget->type(),
                'label'    => $widget->label(),
                'category' => $widget->category(),
                'defaults' => (object) $widget->defaultSettings(),
                'options'  => (object) (method_exists($widget, 'fieldOptions') ? $widget->fieldOptions() : []),
            ];
        }

        return view('admin.editor', ['page' => $page, 'palette' => $palette]);
    }

    /** Current tree as JSON, in the exact shape PageTreeService::sync expects. */
    public function tree(Page $page): JsonResponse
    {
        $page->loadMissing('sections.rows.columns.widgets');

        return response()->json([
            'page'     => ['id' => $page->id, 'title' => $page->title, 'slug' => $page->slug],
            'sections' => $this->serialize($page),
        ]);
    }

    /** Replace the whole tree, then bust the render cache. */
    public function save(Page $page, Request $request, PageTreeService $tree, PageRenderer $renderer): JsonResponse
    {
        $data = $request->validate([
            'sections'                                      => 'present|array',
            'sections.*.type'                               => 'nullable|string|max:50',
            'sections.*.settings'                           => 'nullable|array',
            'sections.*.rows'                               => 'array',
            'sections.*.rows.*.settings'                    => 'nullable|array',
            'sections.*.rows.*.columns'                     => 'array',
            'sections.*.rows.*.columns.*.width'             => 'nullable|integer|min:1|max:12',
            'sections.*.rows.*.columns.*.settings'          => 'nullable|array',
            'sections.*.rows.*.columns.*.widgets'           => 'array',
            'sections.*.rows.*.columns.*.widgets.*.type'    => 'required|string|max:50',
            'sections.*.rows.*.columns.*.widgets.*.settings' => 'nullable|array',
        ]);

        $tree->sync($page, $data['sections']);
        $renderer->forget($page);

        return response()->json(['ok' => true]);
    }

    /** Render the current (unsaved) editor tree for live preview. */
    public function preview(Page $page, Request $request, PageRenderer $renderer): JsonResponse
    {
        $data = $request->validate([
            'sections'                                       => 'present|array',
            'sections.*.type'                                => 'nullable|string|max:50',
            'sections.*.settings'                            => 'nullable|array',
            'sections.*.rows'                                => 'array',
            'sections.*.rows.*.columns'                      => 'array',
            'sections.*.rows.*.columns.*.width'              => 'nullable|integer|min:1|max:12',
            'sections.*.rows.*.columns.*.widgets'            => 'array',
            'sections.*.rows.*.columns.*.widgets.*.type'     => 'required|string|max:50',
            'sections.*.rows.*.columns.*.widgets.*.settings' => 'nullable|array',
        ]);

        return response()->json(['html' => $renderer->renderTree($data['sections'])]);
    }

    /** @return array<int, array<string, mixed>> */
    protected function serialize(Page $page): array
    {
        return $page->sections->map(fn ($section) => [
            'type'     => $section->section_type,
            'settings' => $section->settings,
            'rows'     => $section->rows->map(fn ($row) => [
                'settings' => $row->settings,
                'columns'  => $row->columns->map(fn ($column) => [
                    'width'    => $column->width,
                    'settings' => $column->settings,
                    'widgets'  => $column->widgets->map(fn ($widget) => [
                        'type'     => $widget->widget_type,
                        'settings' => (object) ($widget->settings ?? []),
                    ])->all(),
                ])->all(),
            ])->all(),
        ])->all();
    }
}
