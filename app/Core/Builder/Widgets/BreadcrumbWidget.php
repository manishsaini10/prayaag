<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;
use App\Models\Page;
use Illuminate\Support\Facades\Blade;

class BreadcrumbWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'breadcrumb';
    }

    public function label(): string
    {
        return 'Breadcrumb';
    }

    public function category(): string
    {
        return 'navigation';
    }

    public function defaultSettings(): array
    {
        return [
            'style'              => 'simple',
            'separator'          => 'chevron',
            'home_text'          => 'Home',
            'show_home'          => true,
            'show_current_page'  => true,
            'alignment'          => 'left',
            'background_color'   => '#ffffff',
            'text_color'         => '#374151',
            'accent_color'       => '#4f46e5',
            'overlay_opacity'    => 40,
            'padding_y'          => 'py-4',
            'background_image'   => '',
            'background_video'   => '',
            'show_mobile'        => true,
            'min_height'         => '80px',
            'max_width'          => 'full',
            'width_style'        => 'full',
        ];
    }

    public function fieldOptions(): array
    {
        return [
            'style'     => ['simple', 'gradient', 'modern', 'minimal', 'with-image', 'with-video'],
            'separator' => ['chevron', 'slash', 'dot', 'arrow'],
            'alignment' => ['left', 'center'],
            'min_height' => ['60px', '80px', '100px', '120px', '150px', '200px', '250px', '300px'],
            'max_width' => ['full', '7xl', '6xl', '5xl', '4xl', '3xl', '2xl'],
            'width_style' => ['full', 'box'],
        ];
    }

    public function isDynamic(): bool
    {
        return true;
    }

    public function render(array $settings, array $context = []): string
    {
        $settings  = array_merge($this->defaultSettings(), $settings);
        $pageSlug  = $context['page_slug'] ?? request()->path();
        $pageTitle = $context['page_title'] ?? '';
        $pageSlug  = trim($pageSlug, '/');

        if (empty($pageTitle) && $pageSlug !== '') {
            $page = Page::published()->where('slug', $pageSlug)->first();
            $pageTitle = $page?->title ?: ucfirst(str_replace('-', ' ', basename($pageSlug)));
        }

        $trail = [];
        if ($settings['show_home']) {
            $trail[] = ['label' => $settings['home_text'], 'url' => url('/')];
        }

        $segments = array_values(array_filter(explode('/', $pageSlug)));
        $accumulated = '';
        foreach ($segments as $i => $seg) {
            $accumulated .= '/' . $seg;
            $isLast = $i === count($segments) - 1;
            if ($isLast && $settings['show_current_page']) {
                $trail[] = ['label' => $pageTitle, 'url' => null];
            } elseif (! $isLast) {
                $ancestor = Page::published()->where('slug', ltrim($accumulated, '/'))->first();
                $trail[] = [
                    'label' => $ancestor?->title ?: ucfirst(str_replace('-', ' ', $seg)),
                    'url'   => url($accumulated),
                ];
            }
        }

        return Blade::render('<x-breadcrumb :trail="$trail" :settings="$settings"/>', [
            'trail'    => $trail,
            'settings' => $settings,
        ]);
    }
}
