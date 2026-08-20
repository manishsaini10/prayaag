<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Full-page Mandatory Public Disclosure & CBSE Appendix IX Widget.
 * Renders complete statutory document portal with Key Documents (Fee, Safety Certificates),
 * All 18 compliance dossiers, instant search, and CBSE statutory declaration.
 */
class DisclosurePageWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'disclosure-page';
    }

    public function label(): string
    {
        return 'Mandatory Public Disclosure Page (Full)';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [];
    }

    public function render(array $settings, array $context = []): string
    {
        return view('widgets.disclosure-page')->render();
    }
}
