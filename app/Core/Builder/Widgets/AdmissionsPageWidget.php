<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Full-page premium Admissions widget.
 * Renders the complete admissions page — hero, process steps,
 * stats, eligibility, documents, and CTA — using the school
 * design system tokens from site.css.
 */
class AdmissionsPageWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'admissions-page';
    }

    public function label(): string
    {
        return 'Admissions Page (Full)';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'session'          => '2026–27',
            'apply_url'        => '/registration',
            'whatsapp_number'  => '919350748851',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $session  = $this->e($this->setting($settings, 'session', '2026–27'));
        $applyUrl = $this->e($this->setting($settings, 'apply_url', '#'));
        $wa       = $this->e($this->setting($settings, 'whatsapp_number', ''));
        $waUrl    = $wa ? 'https://wa.me/' . $wa : '#';

        return view('widgets.admissions-page', compact('session', 'applyUrl', 'waUrl'))->render();
    }
}
