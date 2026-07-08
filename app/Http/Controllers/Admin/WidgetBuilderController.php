<?php

namespace App\Http\Controllers\Admin;

use App\Core\Builder\WidgetRegistry;
use App\Http\Controllers\Controller;
use App\Models\WidgetDefinition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * The Widget Builder: lets admins create custom page-builder widgets (name +
 * fields + HTML template) with no code. Saved definitions are picked up by
 * CoreServiceProvider at boot and rendered by DynamicWidget, so a new widget
 * appears in the Page Builder palette immediately after saving.
 */
class WidgetBuilderController extends Controller
{
    public function index(): View
    {
        return view('admin.widget-builder.index', [
            'widgets' => WidgetDefinition::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        $sample = "<div data-reveal style=\"text-align:center\">\n"
            . "  <h2>{{ heading }}</h2>\n"
            . "  <p style=\"max-width:60ch;margin:0 auto\">{{ text }}</p>\n"
            . "</div>";

        return view('admin.widget-builder.form', [
            'widget' => new WidgetDefinition([
                'category'  => 'custom',
                'is_active' => true,
                'fields'    => [
                    ['key' => 'heading', 'label' => 'Heading', 'type' => 'text', 'default' => 'Your heading'],
                    ['key' => 'text', 'label' => 'Text', 'type' => 'textarea', 'default' => 'Some description text.'],
                ],
                'template'  => $sample,
            ]),
        ]);
    }

    public function store(Request $request, WidgetRegistry $registry): RedirectResponse
    {
        WidgetDefinition::create($this->validateData($request, $registry, null));
        Cache::flush();

        return redirect()->route('admin.widgets.index')
            ->with('status', 'Widget created — it is now available in the Page Builder.');
    }

    public function edit(int $id): View
    {
        return view('admin.widget-builder.form', [
            'widget' => WidgetDefinition::findOrFail($id),
        ]);
    }

    public function update(int $id, Request $request, WidgetRegistry $registry): RedirectResponse
    {
        $widget = WidgetDefinition::findOrFail($id);
        $widget->update($this->validateData($request, $registry, $widget));
        Cache::flush();

        return redirect()->route('admin.widgets.index')->with('status', 'Widget updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        WidgetDefinition::findOrFail($id)->delete();
        Cache::flush();

        return redirect()->route('admin.widgets.index')->with('status', 'Widget deleted.');
    }

    /** Validate + normalise the posted definition. */
    protected function validateData(Request $request, WidgetRegistry $registry, ?WidgetDefinition $current): array
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:100',
            'slug'             => 'nullable|string|max:60|regex:/^[a-z0-9\-]+$/',
            'category'         => 'nullable|string|max:50',
            'template'         => 'required|string|max:20000',
            'is_active'        => 'nullable|boolean',
            'fields'           => 'array',
            'fields.*.key'     => 'nullable|string|max:50|regex:/^[a-zA-Z0-9_]+$/',
            'fields.*.label'   => 'nullable|string|max:100',
            'fields.*.type'    => 'nullable|string|max:20',
            'fields.*.default' => 'nullable|string|max:5000',
        ], [
            'slug.regex'         => 'Slug may contain only lowercase letters, numbers and hyphens.',
            'fields.*.key.regex' => 'Field keys may contain only letters, numbers and underscores.',
        ]);

        $slug = $validated['slug'] ?: Str::slug($validated['name']);

        // Reject clashes with another custom widget or a built-in widget type.
        $clashesDb = WidgetDefinition::where('slug', $slug)
            ->when($current, fn ($q) => $q->where('id', '!=', $current->id))
            ->exists();
        $clashesBuiltin = $registry->has($slug) && (! $current || $current->slug !== $slug);

        if ($clashesDb || $clashesBuiltin) {
            throw ValidationException::withMessages([
                'slug' => "The slug \"{$slug}\" is already taken. Choose a different one.",
            ]);
        }

        $fields = array_values(array_filter(
            $validated['fields'] ?? [],
            fn ($f) => ! empty($f['key'])
        ));

        return [
            'name'      => $validated['name'],
            'slug'      => $slug,
            'category'  => $validated['category'] ?: 'custom',
            'template'  => $validated['template'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'fields'    => $fields,
        ];
    }
}
