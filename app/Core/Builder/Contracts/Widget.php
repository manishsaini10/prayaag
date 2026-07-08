<?php

namespace App\Core\Builder\Contracts;

/**
 * A page-builder widget. Implementations register with the WidgetRegistry
 * and are rendered server-side by the PageRenderer. New widgets (including
 * plugin widgets) only need to implement this and register — no core change.
 */
interface Widget
{
    public function type(): string;

    public function label(): string;

    public function category(): string;

    /** @return array<string, mixed> */
    public function defaultSettings(): array;

    /** Whether this widget queries live data (or per-request state) at render time. */
    public function isDynamic(): bool;

    /**
     * @param  array<string, mixed>  $settings
     * @param  array<string, mixed>  $context  Dynamic data-binding context.
     */
    public function render(array $settings, array $context = []): string;
}
