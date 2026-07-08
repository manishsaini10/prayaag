<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

class ImageWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'image';
    }

    public function label(): string
    {
        return 'Image';
    }

    public function category(): string
    {
        return 'media';
    }

    public function defaultSettings(): array
    {
        return ['src' => '', 'alt' => ''];
    }

    public function render(array $settings, array $context = []): string
    {
        $src = $this->e($this->setting($settings, 'src', ''));
        $alt = $this->e($this->setting($settings, 'alt', ''));

        if ($src === '') {
            return '';
        }

        return "<img class=\"pb-image\" src=\"{$src}\" alt=\"{$alt}\">";
    }
}
