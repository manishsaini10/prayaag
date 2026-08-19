<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * PRO Widget — Reviews & Ratings Grid.
 * Customer review cards with 5-star rating badges and trust metrics.
 */
class ReviewsRatingsWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'pro-reviews-ratings';
    }

    public function label(): string
    {
        return 'Reviews & Ratings Grid';
    }

    public function category(): string
    {
        return 'pro-social';
    }

    public function defaultSettings(): array
    {
        return [
            'eyebrow' => 'Parent Feedback & Trust',
            'heading' => 'Rated 4.9/5 by 500+ Parents on Google',
            'sub'     => 'Lorem ipsum dolor sit amet, read what parents say about their experience with Prayaag.',
            'reviews' => [
                [
                    'name'    => 'Suresh Malhotra',
                    'relation'=> 'Parent of Aarav (Class X)',
                    'rating'  => 5,
                    'text'    => 'Lorem ipsum dolor sit amet, highly impressed with the academic environment, dedicated faculty and individual attention given to every child.',
                    'avatar'  => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&auto=format&fit=crop&q=80',
                ],
                [
                    'name'    => 'Priyanka Kapoor',
                    'relation'=> 'Parent of Ishita (Primary Wing)',
                    'rating'  => 5,
                    'text'    => 'The activity-based learning method helped my daughter develop curiosity and confidence. Safe campus and excellent bus transport.',
                    'avatar'  => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=200&auto=format&fit=crop&q=80',
                ],
            ],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        $head = $this->sectionHead(
            $this->setting($settings, 'eyebrow'),
            $this->setting($settings, 'heading'),
            $this->setting($settings, 'sub')
        );

        $reviews = (array) $this->setting($settings, 'reviews', []);
        $cardsHtml = '';

        foreach ($reviews as $r) {
            $name   = $this->e($r['name'] ?? '');
            $rel    = $this->e($r['relation'] ?? '');
            $text   = $this->e($r['text'] ?? '');
            $avatar = $this->e($r['avatar'] ?? '');
            $stars  = str_repeat('★', max(1, min(5, (int) ($r['rating'] ?? 5))));

            $cardsHtml .= <<<HTML
            <div class="ek-rr-card">
                <div class="ek-rr-stars">{$stars}</div>
                <p class="ek-rr-text">"{$text}"</p>
                <div class="ek-rr-author">
                    <img src="{$avatar}" alt="{$name}" class="ek-rr-avatar">
                    <div>
                        <div class="ek-rr-name">{$name} <span class="ek-rr-check">✓ Verified</span></div>
                        <div class="ek-rr-rel">{$rel}</div>
                    </div>
                </div>
            </div>
            HTML;
        }

        return <<<HTML
        <style>
        .ek-rr-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; max-width: 1000px; margin: 30px auto 0; padding: 0 16px; }
        .ek-rr-card { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 18px; padding: 28px; box-shadow: 0 10px 30px rgba(11,37,69,.06); display: flex; flex-direction: column; justify-content: space-between; transition: all .3s ease; }
        .ek-rr-card:hover { border-color: #c79a3b; transform: translateY(-4px); box-shadow: 0 16px 36px rgba(11,37,69,.12); }
        .ek-rr-stars { color: #f59e0b; font-size: 20px; letter-spacing: 2px; margin-bottom: 14px; }
        .ek-rr-text { font-size: 14.5px; color: #475569; line-height: 1.65; margin: 0 0 20px; flex: 1; font-style: italic; }
        .ek-rr-author { display: flex; align-items: center; gap: 12px; border-top: 1px solid #f1f5f9; padding-top: 16px; }
        .ek-rr-avatar { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; }
        .ek-rr-name { font-size: 15px; font-weight: 700; color: #0b2545; }
        .ek-rr-rel { font-size: 12px; color: #64748b; }
        .ek-rr-check { background: rgba(16,185,129,.12); color: #10b981; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px; font-style: normal; margin-left: 4px; }
        </style>

        <section class="ek-rr-sec">
            {$head}
            <div class="ek-rr-grid">
                {$cardsHtml}
            </div>
        </section>
        HTML;
    }
}
