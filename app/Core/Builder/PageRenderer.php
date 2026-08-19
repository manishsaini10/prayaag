<?php

namespace App\Core\Builder;

use App\Models\Page;
use App\Models\PageColumn;
use App\Models\PageRow;
use App\Models\PageSection;
use App\Models\PageWidget;
use Illuminate\Support\Facades\Cache;

/**
 * Rendering engine: DB tree -> section -> row -> column -> widget -> HTML.
 * All output is produced here in PHP via registered widgets; Blade (added
 * later) is a thin renderer only — no business logic lives in templates.
 */
class PageRenderer
{
    protected array $renderContext = [];

    public function __construct(protected WidgetRegistry $widgets)
    {
    }

    public function render(Page $page): string
    {
        $page->loadMissing('sections.rows.columns.widgets');

        $this->renderContext = [
            'page_id'    => $page->id,
            'page_slug'  => $page->slug,
            'page_title' => $page->title,
            'page_url'   => url($page->slug === 'home' ? '/' : $page->slug),
            'page_seo'   => $page->seo ?? [],
        ];

        $html = '';
        foreach ($page->sections as $section) {
            $html .= $this->renderSection($section);
        }

        return $html;
    }

    /**
     * Render an in-memory tree array (the same shape PageTreeService::sync
     * accepts) without persisting it. Used by the editor's live preview.
     *
     * @param  array<int, array<string, mixed>>  $sections
     */
    public function renderTree(array $sections): string
    {
        $this->renderContext = [];

        $html = '';

        foreach ($sections as $section) {
            $rows = '';
            foreach ($section['rows'] ?? [] as $row) {
                $columns = '';
                foreach ($row['columns'] ?? [] as $column) {
                    $widgets = '';
                    foreach ($column['widgets'] ?? [] as $widget) {
                        $widgets .= $this->widgets->render(
                            (string) ($widget['type'] ?? ''),
                            (array) ($widget['settings'] ?? []),
                            $this->renderContext,
                        );
                    }
                    $width = (int) ($column['width'] ?? 12);
                    $colAttr = $this->elementStyleAttr((array) ($column['settings'] ?? []));
                    $columns .= "<div class=\"pb-col pb-col--{$width}\"{$colAttr}>{$widgets}</div>";
                }
                $rowAttr = $this->elementStyleAttr((array) ($row['settings'] ?? []));
                $rows .= "<div class=\"pb-row\"{$rowAttr}>{$columns}</div>";
            }
            $type = htmlspecialchars((string) ($section['type'] ?? 'section'), ENT_QUOTES, 'UTF-8');
            [$cls, $attr] = $this->sectionExtras((array) ($section['settings'] ?? []));
            $html .= "<section class=\"pb-section pb-section--{$type}{$cls}\"{$attr}>{$rows}</section>";
        }

        return $html;
    }

    /** Cached variant keyed per page. Call forget() on save. */
    public function renderCached(Page $page): string
    {
        // Never cache pages containing dynamic widgets: their content (and the
        // CSRF tokens in form widgets) must be produced fresh per request.
        if ($this->hasDynamicWidgets($page)) {
            return $this->render($page);
        }

        return Cache::rememberForever($this->cacheKey($page), fn () => $this->render($page));
    }

    protected function hasDynamicWidgets(Page $page): bool
    {
        $page->loadMissing('sections.rows.columns.widgets');

        foreach ($page->sections as $section) {
            foreach ($section->rows as $row) {
                foreach ($row->columns as $column) {
                    foreach ($column->widgets as $widget) {
                        $resolved = $this->widgets->get($widget->widget_type);
                        if ($resolved && $resolved->isDynamic()) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    public function forget(Page $page): void
    {
        Cache::forget($this->cacheKey($page));
    }

    protected function cacheKey(Page $page): string
    {
        return "page-render:{$page->id}";
    }

    protected function renderSection(PageSection $section): string
    {
        $rows = '';
        foreach ($section->rows as $row) {
            $rows .= $this->renderRow($row);
        }

        $type = htmlspecialchars((string) ($section->section_type ?: 'section'), ENT_QUOTES, 'UTF-8');
        [$cls, $attr] = $this->sectionExtras((array) ($section->settings ?? []));

        return "<section class=\"pb-section pb-section--{$type}{$cls}\"{$attr}>{$rows}</section>";
    }

    /**
     * Build optional class suffix + attribute string from a section's settings.
     * Supports CMS-controlled entrance animation, responsive visibility, and custom colors.
     *
     * @return array{0:string,1:string} [classSuffix, attrString]
     */
    protected function sectionExtras(array $settings): array
    {
        $classes = [];
        $attrs = [];
        $styles = [];

        $customBg = (string) ($settings['_custom_bg'] ?? '');
        if ($customBg !== '') {
            $styles[] = 'background-color: ' . htmlspecialchars($customBg, ENT_QUOTES, 'UTF-8');
        }

        $customText = (string) ($settings['_custom_text'] ?? '');
        if ($customText !== '') {
            $styles[] = 'color: ' . htmlspecialchars($customText, ENT_QUOTES, 'UTF-8');
        }

        $anim = (string) ($settings['_animation'] ?? '');
        if ($anim !== '' && $anim !== 'none') {
            $map = ['fade-up' => '', 'fade-down' => 'down', 'fade-left' => 'left', 'fade-right' => 'right', 'zoom' => 'zoom'];
            $val = $map[$anim] ?? '';
            $attrs[] = $val !== '' ? 'data-reveal="' . $val . '"' : 'data-reveal';
            $delay = (int) ($settings['_delay'] ?? 0);
            if ($delay >= 1 && $delay <= 6) {
                $attrs[] = 'data-reveal-delay="' . $delay . '"';
            }
        }
        if (! empty($settings['_hide_mobile'])) {
            $classes[] = 'hide-mobile';
        }
        if (! empty($settings['_hide_desktop'])) {
            $classes[] = 'hide-desktop';
        }
        if (! empty($settings['_no_hover'])) {
            $classes[] = 'pb-no-hover';
        }

        if (! empty($styles)) {
            $attrs[] = 'style="' . implode('; ', $styles) . '"';
        }

        return [
            $classes ? ' ' . implode(' ', $classes) : '',
            $attrs ? ' ' . implode(' ', $attrs) : '',
        ];
    }

    protected function elementStyleAttr(array $settings): string
    {
        $styles = [];
        if (! empty($settings['_custom_bg'])) {
            $styles[] = 'background-color: ' . htmlspecialchars((string) $settings['_custom_bg'], ENT_QUOTES, 'UTF-8');
        }
        if (! empty($settings['_custom_text'])) {
            $styles[] = 'color: ' . htmlspecialchars((string) $settings['_custom_text'], ENT_QUOTES, 'UTF-8');
        }

        return ! empty($styles) ? ' style="' . implode('; ', $styles) . '"' : '';
    }

    protected function renderRow(PageRow $row): string
    {
        $columns = '';
        foreach ($row->columns as $column) {
            $columns .= $this->renderColumn($column);
        }
        $rowAttr = $this->elementStyleAttr((array) ($row->settings ?? []));

        return "<div class=\"pb-row\"{$rowAttr}>{$columns}</div>";
    }

    protected function renderColumn(PageColumn $column): string
    {
        $widgets = '';
        foreach ($column->widgets as $widget) {
            $widgets .= $this->renderWidget($widget);
        }

        $width = (int) ($column->width ?: 12);
        $colAttr = $this->elementStyleAttr((array) ($column->settings ?? []));

        return "<div class=\"pb-col pb-col--{$width}\"{$colAttr}>{$widgets}</div>";
    }

    protected function renderWidget(PageWidget $widget): string
    {
        return $this->widgets->render(
            $widget->widget_type,
            (array) ($widget->settings ?? []),
            $this->renderContext,
        );
    }
}
