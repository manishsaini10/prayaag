<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Event;
use App\Models\Page;
use Illuminate\Database\Seeder;

class EventsPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'events'],
            ['title' => 'Events', 'status' => 'published']
        );

        $b = '/storage/media/imported/';

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'Events — Prayaag International School, Panipat',
                'description' => 'Stay updated with the latest events, celebrations, and activities at Prayaag International School, Panipat.',
                'og_image'    => $b . '01KWECDVNZP018Q222VPQS6E7C.jpg',
            ],
        ]);

        // Build past events HTML from Event model
        $pastEvents = Event::where('starts_at', '<', now())->orderBy('starts_at', 'desc')->get();
        $pastHtml = '<div class="sec-head" data-reveal>'
            . '<span class="eyebrow">Past Events</span>'
            . '<h2 class="sec-title">Events Gallery</h2>'
            . '</div>'
            . '<div class="events-past-grid" data-reveal>';

        foreach ($pastEvents as $ev) {
            $date = $ev->starts_at?->format('M j, Y') ?? '';
            $pastHtml .= '<div class="ep-card">'
                . '<div class="ep-date"><span class="ep-day">' . $ev->starts_at?->format('d') ?? '' . '</span><span class="ep-month">' . $ev->starts_at?->format('M') ?? '' . '</span></div>'
                . '<div class="ep-info"><h4>' . e($ev->title) . '</h4>'
                . '<span class="ep-meta">' . e($date) . ($ev->location ? ' &middot; ' . e($ev->location) : '') . '</span>'
                . ($ev->description ? '<p>' . e($ev->description) . '</p>' : '')
                . '</div></div>';
        }

        $pastHtml .= '</div>';

        $sections = [
            // 1. Hero
            [
                'type' => 'flush',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'hero',
                            'settings' => [
                                'kicker'          => 'School Events',
                                'heading'         => 'Events',
                                'tagline'         => 'Stay connected with the vibrant calendar of events at Prayag International School.',
                                'image'           => $b . '01KWECDVNZP018Q222VPQS6E7C.jpg',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 2. Upcoming Events (dynamic)
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'upcoming_events',
                            'settings' => [
                                'limit'  => 10,
                                'layout' => 'grid',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 3. Past Events
            [
                'type' => 'alt',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => $pastHtml,
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Events page created with ' . count($sections) . ' sections.');
    }
}
