<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Library Page Widget — Digital Library, Reading Programs & Book Catalogue
 */
class LibraryPageWidget extends AbstractWidget
{
    public function type(): string { return 'library-page'; }
    public function label(): string { return 'Library & Learning Resource Centre (Full Page)'; }
    public function category(): string { return 'school'; }

    public function defaultSettings(): array
    {
        return [
            'hero_title'    => 'Library & Learning Resource Centre',
            'hero_subtitle' => 'A vibrant knowledge hub with over 15,000 curated books, digital e-resources, and reading programs that cultivate a lifelong love for learning across all age groups.',
            'hero_bg'       => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Library.webp',

            'stats' => [
                ['icon' => '📚', 'value' => '15,000+', 'label' => 'Curated Books'],
                ['icon' => '💻', 'value' => '50+',     'label' => 'Digital E-Resources'],
                ['icon' => '📰', 'value' => '30+',     'label' => 'Journals & Magazines'],
                ['icon' => '🎧', 'value' => 'A/V Zone', 'label' => 'Audio-Visual Section'],
            ],

            'sections' => [
                [
                    'icon'  => '📖',
                    'title' => 'Rich & Diverse Book Collection',
                    'desc'  => 'The library houses over 15,000 titles spanning fiction, non-fiction, science, humanities, competitive exam preparation, and reference books. Books are categorized by class, subject, and Dewey Decimal System for easy access.',
                ],
                [
                    'icon'  => '💡',
                    'title' => 'Digital & E-Learning Resources',
                    'desc'  => 'Students have access to online databases, NCERT digital textbooks, e-journals, and curated educational YouTube channels via dedicated computer stations inside the library.',
                ],
                [
                    'icon'  => '🏆',
                    'title' => 'Reading Programs & Competitions',
                    'desc'  => 'Year-round reading challenges, book reviews, storytelling sessions, and inter-class Reading Olympiad keep students motivated. Top readers receive the prestigious "Prayaag Reading Star" award quarterly.',
                ],
                [
                    'icon'  => '🤫',
                    'title' => 'Dedicated Study & Silent Zones',
                    'desc'  => 'Designated silent reading areas and group study rooms provide a calm, focused environment for exam preparation, project work, and independent research.',
                ],
                [
                    'icon'  => '📰',
                    'title' => 'Newspapers, Journals & Magazines',
                    'desc'  => 'Daily national newspapers (English & Hindi), competitive magazines like Competition Success Review, Science Reporter, Pratiyogita Darpan, and international journals are available for students and faculty.',
                ],
                [
                    'icon'  => '📅',
                    'title' => 'Flexible Borrowing Policy',
                    'desc'  => 'Students may borrow up to 2 books simultaneously for a 2-week period. Teachers have extended borrowing privileges. An automated barcode system tracks all issues and returns.',
                ],
            ],

            'timings' => [
                ['day' => 'Monday – Friday', 'time' => '08:00 AM – 04:00 PM'],
                ['day' => 'Saturday',         'time' => '08:00 AM – 01:00 PM'],
                ['day' => 'Sunday',           'time' => 'Closed'],
            ],

            'cta_url'   => '/contact',
            'cta_label' => 'Contact Library Team',
        ];
    }

    public function settingsSchema(): array
    {
        return [
            ['key' => 'hero_title',    'label' => 'Hero Title',    'type' => 'text'],
            ['key' => 'hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea'],
            ['key' => 'cta_label',     'label' => 'CTA Button Text', 'type' => 'text'],
            ['key' => 'cta_url',       'label' => 'CTA Button URL',  'type' => 'text'],
        ];
    }

    public function render(array $settings, array $context = []): string
    {
        return view('widgets.library-page', $settings)->render();
    }
}
