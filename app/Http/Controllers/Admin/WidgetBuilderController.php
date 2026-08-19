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
 *
 * The WidgetStudio tab exposes all built-in registered widgets as a
 * Widget Studio Library, letting admins one-click-seed any built-in into the DB for
 * further customisation.
 */
class WidgetBuilderController extends Controller
{
    public function index(WidgetRegistry $registry): View
    {
        $customWidgets = WidgetDefinition::orderBy('name')->get();

        // Build WidgetStudio library from all registered widgets
        $allWidgets = collect($registry->all())->map(function ($widget) use ($customWidgets) {
            $seeded = $customWidgets->firstWhere('slug', $widget->type());
            return [
                'type'        => $widget->type(),
                'label'       => $widget->label(),
                'category'    => $widget->category(),
                'is_dynamic'  => $widget->isDynamic(),
                'is_seeded'   => $seeded !== null,
                'seeded_id'   => $seeded?->id,
                'defaults'    => $widget->defaultSettings(),
                'field_count' => count($widget->defaultSettings()),
            ];
        })->sortBy('label')->groupBy('category');

        $categoryMeta = [
            'hero'         => ['icon' => '🏛️',  'label' => 'Hero & Banners'],
            'school'       => ['icon' => '🎓',  'label' => 'School Modules'],
            'content'      => ['icon' => '📝',  'label' => 'Content Blocks'],
            'media'        => ['icon' => '🖼️',  'label' => 'Media & Gallery'],
            'forms'        => ['icon' => '📩',  'label' => 'Forms & Leads'],
            'dynamic'      => ['icon' => '⚡',  'label' => 'Dynamic Widgets'],
            'general'      => ['icon' => '🧩',  'label' => 'General Elements'],
            'pro-general'  => ['icon' => '👑',  'label' => 'PRO General'],
            'pro-advanced' => ['icon' => '👑',  'label' => 'PRO Advanced'],
            'pro-creative' => ['icon' => '👑',  'label' => 'PRO Creative'],
            'pro-features' => ['icon' => '👑',  'label' => 'PRO Features & Marketing'],
            'pro-social'   => ['icon' => '👑',  'label' => 'PRO Reviews & Social'],
            'custom'       => ['icon' => '✨',  'label' => 'Custom'],
        ];

        return view('admin.widget-builder.index', compact('customWidgets', 'allWidgets', 'categoryMeta'));
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

    /**
     * Seed a built-in registered widget into the DB as a WidgetDefinition,
     * making it visible in "My Widgets" and customisable via the form editor.
     */
    public function seed(string $type, WidgetRegistry $registry): RedirectResponse
    {
        $widget = $registry->get($type);

        if (! $widget) {
            return back()->with('error', "Widget type \"{$type}\" not found in registry.");
        }

        // Prevent duplicate seeds
        if (WidgetDefinition::where('slug', $type)->exists()) {
            return redirect()->route('admin.widgets.index')
                ->with('status', "\"{$widget->label()}\" is already in My Widgets.");
        }

        // Render full HTML output with default settings
        $defaults = $widget->defaultSettings();
        $fullHtml = $widget->render($defaults);
        $template = $fullHtml;

        // Convert defaultSettings to field definitions and interpolate into template
        $fields = [];
        foreach ($defaults as $key => $default) {
            if (is_array($default)) continue; // Skip complex array fields
            $valStr = (string) $default;
            if ($valStr !== '') {
                $template = str_replace(htmlspecialchars($valStr, ENT_QUOTES, 'UTF-8'), "{{ {$key} }}", $template);
                $template = str_replace($valStr, "{{ {$key} }}", $template);
            }
            $fieldType = is_numeric($default) ? 'number' : (strlen($valStr) > 80 ? 'textarea' : 'text');
            $fields[] = [
                'key'     => $key,
                'label'   => ucwords(str_replace('_', ' ', $key)),
                'type'    => $fieldType,
                'default' => $valStr,
            ];
        }

        WidgetDefinition::create([
            'name'      => $widget->label(),
            'slug'      => $type,
            'category'  => $widget->category(),
            'template'  => $template,
            'fields'    => $fields,
            'is_active' => true,
        ]);

        Cache::flush();

        return redirect()->route('admin.widgets.index')
            ->with('status', "✅ \"{$widget->label()}\" added to My Widgets — you can now customise it.");
    }

    /**
     * Render a widget in an isolated full-page preview with site.css.
     * Used inside an iframe so it picks up the real frontend styles.
     */
    public function preview(string $type, WidgetRegistry $registry): \Illuminate\Http\Response
    {
        $widget = $registry->get($type);

        if (! $widget) {
            return response("<p style='font-family:sans-serif;padding:40px;color:#ef4444;text-align:center'>Widget type <strong>{$type}</strong> not found in registry.</p>");
        }

        $widgetHtml = $widget->render($widget->defaultSettings(), []);

        return response(view('admin.widget-builder.preview', [
            'label'     => $widget->label(),
            'type'      => $type,
            'category'  => $widget->category(),
            'html'      => $widgetHtml,
            'isDynamic' => $widget->isDynamic(),
        ]));
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
