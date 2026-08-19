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
        return ['src' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/01/About-Prayaag-International-School.webp', 'alt' => 'Prayaag International School'];
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
