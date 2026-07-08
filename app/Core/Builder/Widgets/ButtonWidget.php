<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

class ButtonWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'button';
    }

    public function label(): string
    {
        return 'Button';
    }

    public function defaultSettings(): array
    {
        return ['label' => 'Button', 'url' => '#'];
    }

    public function render(array $settings, array $context = []): string
    {
        $label = $this->e($this->setting($settings, 'label', 'Button'));
        $url = $this->e($this->setting($settings, 'url', '#'));

        return "<a class=\"pb-button\" href=\"{$url}\">{$label}</a>";
    }
}
