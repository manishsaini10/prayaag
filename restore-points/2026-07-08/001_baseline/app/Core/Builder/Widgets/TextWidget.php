<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

class TextWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'text';
    }

    public function label(): string
    {
        return 'Text';
    }

    public function defaultSettings(): array
    {
        return ['content' => ''];
    }

    public function render(array $settings, array $context = []): string
    {
        $content = nl2br($this->e($this->setting($settings, 'content', '')));

        return "<div class=\"pb-text\">{$content}</div>";
    }
}
