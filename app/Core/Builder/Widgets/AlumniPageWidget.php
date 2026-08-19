<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Full-page premium Alumni widget.
 * Renders a beautiful alumni page — hero, highlights, network cards, CTA.
 * Uses school design system tokens from site.css (navy, gold, Playfair/Poppins).
 */
class AlumniPageWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'alumni-page';
    }

    public function label(): string
    {
        return 'Alumni Page (Full)';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'contact_email' => 'alumni@pisp.in',
            'whatsapp'      => '919350748851',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $email    = $this->e($this->setting($settings, 'contact_email', 'alumni@pisp.in'));
        $wa       = $this->setting($settings, 'whatsapp', '');
        $waUrl    = $wa ? 'https://wa.me/' . $wa : '#';

        return view('widgets.alumni-page', compact('email', 'waUrl'))->render();
    }
}
