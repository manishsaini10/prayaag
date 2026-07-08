<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

class HeadingWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'heading';
    }

    public function label(): string
    {
        return 'Heading';
    }

    public function defaultSettings(): array
    {
        return ['text' => 'Heading', 'level' => 2];
    }

    public function render(array $settings, array $context = []): string
    {
        $level = max(1, min(6, (int) $this->setting($settings, 'level', 2)));
        $text = $this->e($this->setting($settings, 'text', ''));

        return "<h{$level} class=\"pb-heading\">{$text}</h{$level}>";
    }
}
