<?php

namespace App\Core\Builder;

use App\Core\Builder\Contracts\Widget;

abstract class AbstractWidget implements Widget
{
    public function category(): string
    {
        return 'general';
    }

    public function defaultSettings(): array
    {
        return [];
    }

    /**
     * Optional per-field dropdown options, keyed by setting name.
     * e.g. ['layout' => ['cards', 'slider']] makes the editor show a select.
     *
     * @return array<string, array<int, string>>
     */
    public function fieldOptions(): array
    {
        return [];
    }

    /** Static by default; dynamic widgets (live queries, forms) override to true. */
    public function isDynamic(): bool
    {
        return false;
    }

    /** Resolve a setting value, falling back to defaults. */
    protected function setting(array $settings, string $key, mixed $default = null): mixed
    {
        return $settings[$key] ?? $this->defaultSettings()[$key] ?? $default;
    }

    /** Escape a value for safe HTML output. */
    protected function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    /** Shared centered section header (eyebrow + title + sub). */
    protected function sectionHead(?string $eyebrow, ?string $heading, ?string $sub = null): string
    {
        if (! $eyebrow && ! $heading && ! $sub) {
            return '';
        }

        $html = '<div class="sec-head" data-reveal>';
        if ($eyebrow) {
            $html .= '<span class="eyebrow">' . $this->e($eyebrow) . '</span>';
        }
        if ($heading) {
            $html .= '<h2 class="sec-title">' . $this->e($heading) . '</h2>';
        }
        if ($sub) {
            $html .= '<p class="sec-sub">' . $this->e($sub) . '</p>';
        }

        return $html . '</div>';
    }

    /** Two-letter initials from a name (for avatars). */
    protected function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $a = mb_substr($parts[0] ?? '', 0, 1);
        $b = mb_substr(end($parts) ?: '', 0, 1);

        return mb_strtoupper($a . $b);
    }
}
