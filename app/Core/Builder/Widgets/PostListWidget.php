<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Suite — Post List Layout.
 * Horizontal article list cards with thumbnails, author metadata & summary.
 */
class PostListWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-post-list';
    }

    public function label(): string
    {
        return 'Post List Layout';
    }

    public function category(): string
    {
        return 'pro-advanced';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Latest Articles',
            'heading' => 'Recent Campus Stories & News',
            'posts'   => [
                [
                    'title'   => 'Prayaag Students Win Top Honors at National Science & Robotics Fair 2026',
                    'date'    => '12th August 2026',
                    'read'    => '4 min read',
                    'summary' => 'Lorem ipsum dolor sit amet, our young innovators bagged gold medals for their AI traffic management prototype.',
                    'image'   => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=400&auto=format&fit=crop&q=80',
                    'url'     => '/news/robotics-fair',
                ],
                [
                    'title'   => 'Annual Inter-School Athletics Meet Concludes with 15 Gold Medals',
                    'date'    => '10th August 2026',
                    'read'    => '3 min read',
                    'summary' => 'Lorem ipsum dolor sit amet, inspiring sportsmanship witnessed during track and field events at Olympic Stadium.',
                    'image'   => 'https://images.unsplash.com/photo-1576610616656-d3aa5d1f4534?w=400&auto=format&fit=crop&q=80',
                    'url'     => '/news/sports-meet',
                ],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading')
        );

        $posts = (array) $this->setting($settings, 'posts', []);
        $itemsHtml = '';

        foreach ($posts as $p) {
            $title   = $this->e($p['title'] ?? '');
            $date    = $this->e($p['date'] ?? '');
            $read    = $this->e($p['read'] ?? '');
            $summary = $this->e($p['summary'] ?? '');
            $img     = $this->e($p['image'] ?? '');
            $url     = $this->e($p['url'] ?? '#');

            $itemsHtml .= <<<HTML
            <article class="pro-postl-card">
                <img src="{$img}" alt="{$title}" class="pro-postl-img">
                <div class="pro-postl-body">
                    <div class="pro-postl-meta">📅 {$date} &nbsp;•&nbsp; ⏱️ {$read}</div>
                    <h3 class="pro-postl-title"><a href="{$url}" class="pro-postl-link">{$title}</a></h3>
                    <p class="pro-postl-summary">{$summary}</p>
                </div>
            </article>
            HTML;
        }

        return <<<HTML
        <style>
        .pro-postl-container { max-width: 900px; margin: 30px auto 0; padding: 0 16px; display: flex; flex-direction: column; gap: 20px; }
        .pro-postl-card { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 18px; padding: 20px; display: flex; gap: 20px; box-shadow: 0 8px 24px rgba(11,37,69,.06); transition: all .3s ease; align-items: center; }
        .pro-postl-card:hover { border-color: #c79a3b; transform: translateY(-3px); box-shadow: 0 14px 32px rgba(11,37,69,.12); }
        .pro-postl-img { width: 160px; height: 110px; border-radius: 12px; object-fit: cover; flex-shrink: 0; }
        .pro-postl-body { flex: 1; }
        .pro-postl-meta { font-size: 12px; font-weight: 600; color: #c79a3b; margin-bottom: 6px; }
        .pro-postl-title { font-size: 17px; font-weight: 800; margin: 0 0 8px; line-height: 1.3; }
        .pro-postl-link { color: #0b2545; text-decoration: none; transition: color .2s; }
        .pro-postl-link:hover { color: #c79a3b; }
        .pro-postl-summary { font-size: 13.5px; color: #64748b; margin: 0; line-height: 1.5; }
        @media(max-width: 600px) { .pro-postl-card { flex-direction: column; text-align: center; } .pro-postl-img { width: 100%; height: 160px; } }
        </style>

        <section class="pro-postl-sec">
            {$head}
            <div class="pro-postl-container">
                {$itemsHtml}
            </div>
        </section>
        HTML;
    }
}
