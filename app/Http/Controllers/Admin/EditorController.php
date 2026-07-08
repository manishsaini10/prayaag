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
        $pages = Page::orderBy('title')->get(['id', 'title', 'slug', 'status']);

        return view('admin.pages-index', ['pages' => $pages]);
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
