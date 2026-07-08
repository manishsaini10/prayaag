<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;
use App\Core\Builder\Concerns\RendersCollection;
use App\Models\Notice;
use Illuminate\Support\Str;

/**
 * Dynamic widget: active notices (pinned first), in 8 premium layouts.
 */
class NoticeBoardWidget extends AbstractWidget
{
    use RendersCollection;

    public function type(): string
    {
        return 'notice_board';
    }

    public function label(): string
    {
        return 'Notice Board';
    }

    public function category(): string
    {
        return 'content';
    }

    public function defaultSettings(): array
    {
        return $this->collectionDefaults('minimal', 10);
    }

    public function fieldOptions(): array
    {
        return ['layout' => $this->collectionLayouts()];
    }

    public function isDynamic(): bool
    {
        return true;
    }

    public function render(array $settings, array $context = []): string
    {
        $limit = max(1, (int) $this->setting($settings, 'limit', 10));

        $notices = Notice::active()
            ->orderByDesc('is_pinned')
            ->latest()
            ->limit($limit)
            ->get();

        $icon = $this->collectionIcon('bell');
        $items = [];
        foreach ($notices as $notice) {
            $body = (string) ($notice->body ?? $notice->content ?? $notice->description ?? '');
            if ($body !== '') {
                $body = Str::limit(trim(strip_tags($body)), 140);
            }
            $slug = (string) ($notice->slug ?? '');

            $items[] = [
                'icon'    => $icon,
                'eyebrow' => ! empty($notice->is_pinned) ? 'Pinned' : '',
                'title'   => (string) $notice->title,
                'text'    => $body,
                'meta'    => optional($notice->created_at)->format('M j, Y') ?: '',
                'href'    => $slug !== '' ? '/' . $this->e($slug) : '',
            ];
        }

        return $this->renderCollection($items, $settings, ['accent' => '#c79a3b', 'default' => 'minimal']);
    }
}
