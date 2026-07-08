<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/** Interactive map section (Google Maps embed). */
class MapWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'map';
    }

    public function label(): string
    {
        return 'Map (interactive)';
    }

    public function category(): string
    {
        return 'school';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Visit Us',
            'heading' => 'Find Us on the Map',
            'sub'     => '',
            'embed'   => '<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d13914.976184424926!2d76.986936!3d29.3191828!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xd337897af9217763!2sPrayaag%20International%20School%2C%20Panipat!5e0!3m2!1sen!2sin!4v1640849540342!5m2!1sen!2sin" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading'),
            $this->setting($settings, 'sub')
        );

        $embed = $this->setting($settings, 'embed');
        if (! $embed) {
            return $head;
        }

        return $head . '<div class="map-embed" data-reveal>' . $embed . '</div>';
    }
}
