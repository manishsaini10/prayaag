<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Full-page premium Academic Downloads Resource Center Widget.
 * Renders the complete interactive downloads catalogue — hero, category tabs
 * (PT-1 Syllabus, Holiday Homework, Mess Menus, Mandatory Disclosures),
 * live search bar, and download cards with direct PDF access.
 */
class DownloadsPageWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'downloads-page';
    }

    public function label(): string
    {
        return 'Downloads Page (Full)';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'session'         => '2025–26',
            'whatsapp_number' => '919350748851',
            'phone'           => '+919350748851',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $session  = (string) $this->setting($settings, 'session', '2025–26');
        $whatsapp = (string) $this->setting($settings, 'whatsapp_number', '919350748851');
        $phone    = (string) $this->setting($settings, 'phone', '+919350748851');

        return view('widgets.downloads-page', compact('session', 'whatsapp', 'phone'))->render();
    }
}
