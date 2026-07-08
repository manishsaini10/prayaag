<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * A page-builder widget defined entirely from the database (the Widget Builder).
 * One instance is created per active WidgetDefinition at boot, so admin-created
 * widgets behave exactly like the hand-coded ones — they appear in the palette,
 * expose editable settings, and render on pages.
 *
 * Template interpolation:
 *   {{ key }}    -> field value, HTML-escaped (safe for text)
 *   {{{ key }}}  -> field value, raw (for embeds / trusted HTML)
 * Unknown keys render as an empty string.
 */
class DynamicWidget extends AbstractWidget
{
    /**
     * @param  array<int, array<string, mixed>>  $fields  [{key,label,type,default}]
     */
    public function __construct(
        protected string $slug,
        protected string $name,
        protected string $cat,
        protected array $fields,
        protected string $template,
    ) {
    }

    public function type(): string
    {
        return $this->slug;
    }

    public function label(): string
    {
        return $this->name;
    }

    public function category(): string
    {
        return $this->cat ?: 'custom';
    }

    public function defaultSettings(): array
    {
        $out = [];
        foreach ($this->fields as $field) {
            $key = $field['key'] ?? '';
            if ($key !== '') {
                $out[$key] = $field['default'] ?? '';
            }
        }

        return $out;
    }

    public function render(array $settings, array $context = []): string
    {
        $data = array_merge($this->defaultSettings(), $settings);
        $tpl = $this->template;

        // Raw placeholders first: {{{ key }}}
        $tpl = preg_replace_callback('/\{\{\{\s*([a-zA-Z0-9_\-.]+)\s*\}\}\}/', function ($m) use ($data) {
            return (string) ($data[$m[1]] ?? '');
        }, $tpl);

        // Escaped placeholders: {{ key }}
        $tpl = preg_replace_callback('/\{\{\s*([a-zA-Z0-9_\-.]+)\s*\}\}/', function ($m) use ($data) {
            return htmlspecialchars((string) ($data[$m[1]] ?? ''), ENT_QUOTES, 'UTF-8');
        }, $tpl);

        return $tpl;
    }
}
