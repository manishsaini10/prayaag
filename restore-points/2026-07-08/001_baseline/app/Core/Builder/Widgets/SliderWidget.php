<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;
use App\Models\Slider;

/**
 * Dynamic widget: renders a slider (by location) with its slides.
 */
class SliderWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'slider';
    }

    public function label(): string
    {
        return 'Slider';
    }

    public function category(): string
    {
        return 'media';
    }

    public function defaultSettings(): array
    {
        return ['location' => 'homepage'];
    }

    public function isDynamic(): bool
    {
        return true;
    }

    public function render(array $settings, array $context = []): string
    {
        $location = (string) $this->setting($settings, 'location', 'homepage');

        $slider = Slider::published()->location($location)->with('slides')->first();

        if (! $slider || $slider->slides->isEmpty()) {
            return '<div class="pb-slider pb-empty"></div>';
        }

        $slides = '';
        foreach ($slider->slides as $slide) {
            $caption = '';
            if ($slide->heading || $slide->subheading) {
                $caption = '<div class="pb-slide__caption">'
                    . ($slide->heading ? '<h3>' . $this->e($slide->heading) . '</h3>' : '')
                    . ($slide->subheading ? '<p>' . $this->e($slide->subheading) . '</p>' : '')
                    . ($slide->link_url
                        ? '<a class="pb-button" href="' . $this->e($slide->link_url) . '">'
                            . $this->e($slide->link_label ?: 'Learn more') . '</a>'
                        : '')
                    . '</div>';
            }

            $slides .= '<div class="pb-slide">'
                . '<img src="' . $this->e($slide->image) . '" alt="' . $this->e($slide->heading ?: '') . '">'
                . $caption
                . '</div>';
        }

        return '<div class="pb-slider" data-slider>' . $slides . '</div>';
    }
}
