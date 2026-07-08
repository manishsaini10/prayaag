<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;
use App\Core\Builder\Concerns\RendersCollection;
use App\Models\Event;
use Illuminate\Support\Str;

/**
 * Dynamic widget: upcoming events (soonest first), in 8 premium layouts.
 */
class UpcomingEventsWidget extends AbstractWidget
{
    use RendersCollection;

    public function type(): string
    {
        return 'upcoming_events';
    }

    public function label(): string
    {
        return 'Upcoming Events';
    }

    public function category(): string
    {
        return 'content';
    }

    public function defaultSettings(): array
    {
        return $this->collectionDefaults('timeline', 6);
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
        $limit = max(1, (int) $this->setting($settings, 'limit', 6));
        $events = Event::upcoming()->orderBy('starts_at')->limit($limit)->get();

        $icon = $this->collectionIcon('calendar');
        $items = [];
        foreach ($events as $event) {
            $image = (string) ($event->image ?? $event->cover ?? $event->banner ?? '');
            $desc  = (string) ($event->excerpt ?? $event->description ?? '');
            if ($desc !== '') {
                $desc = Str::limit(trim(strip_tags($desc)), 130);
            }
            $location = (string) ($event->location ?? $event->venue ?? '');
            $slug     = (string) ($event->slug ?? '');

            $items[] = [
                'image'   => $image,
                'icon'    => $image === '' ? $icon : '',
                'eyebrow' => optional($event->starts_at)->format('D, M j') ?: '',
                'title'   => (string) $event->title,
                'text'    => $desc,
                'meta'    => trim($location . (($location && optional($event->starts_at)->format('g:i A')) ? ' · ' : '') . (optional($event->starts_at)->format('g:i A') ?: '')),
                'href'    => $slug !== '' ? '/' . $this->e($slug) : '',
            ];
        }

        return $this->renderCollection($items, $settings, ['accent' => '#0e7490', 'default' => 'timeline']);
    }
}
