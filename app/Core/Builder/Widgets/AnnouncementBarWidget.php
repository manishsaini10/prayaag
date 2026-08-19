<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/** Thin full-bleed announcement strip with an optional link. */
class AnnouncementBarWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'announcement-bar';
    }

    public function label(): string
    {
        return 'Announcement Bar';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'text'       => 'Admissions Open for 2026-27 — limited seats available.',
            'link_label' => 'Apply Now',
            'link_url'   => '/registration',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $text = $this->e($this->setting($settings, 'text'));
        $link = '';
        if ($label = $this->setting($settings, 'link_label')) {
            $link = ' <a href="' . $this->e($this->setting($settings, 'link_url', '#')) . '">' . $this->e($label) . '</a>';
        }

        return '<div class="fullbleed announce"><div class="container" data-reveal><span>' . $text . '</span>' . $link . '</div></div>';
    }
}
