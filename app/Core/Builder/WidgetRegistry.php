<?php

namespace App\Core\Builder;

use App\Core\Builder\Contracts\Widget;

/**
 * Registry of available widget types. Bound as a singleton; default widgets
 * are registered in CoreServiceProvider::boot. Plugins register their own.
 */
class WidgetRegistry
{
    /** @var array<string, Widget> */
    protected array $widgets = [];

    public function register(Widget $widget): void
    {
        $this->widgets[$widget->type()] = $widget;
    }

    public function get(string $type): ?Widget
    {
        return $this->widgets[$type] ?? null;
    }

    public function has(string $type): bool
    {
        return isset($this->widgets[$type]);
    }

    /** @return array<string, Widget> */
    public function all(): array
    {
        return $this->widgets;
    }

    public function render(string $type, array $settings, array $context = []): string
    {
        $widget = $this->get($type);

        return $widget ? $widget->render($settings, $context) : '';
    }
}
