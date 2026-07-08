<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * Builds the Prayaag International School home page as a Page Builder tree —
 * one section per home block, each holding a single full-width widget that
 * renders the (editable) content. After this runs, the home page is 100%
 * builder-driven and fully manageable from the admin Page Builder.
 *
 * Run: php artisan db:seed --class=Database\\Seeders\\HomePageSeeder
 */
class HomePageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'home'],
            ['title' => 'Home', 'status' => 'published']
        );

        $page->update([
            'title'  => $page->title ?: 'Home',
            'status' => 'published',
            'seo'    => [
                'title'       => 'PISP, Best CBSE School in Panipat | Top Schools in Samalkha',
                'description' => 'Top School in Panipat 2025-26. Best CBSE Affiliated Play/Preschool, Secondary and Senior Sec. Schools in Panipat. Top Schools in Samalkha.',
                'og_image'    => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/01/About-Prayaag-International-School.webp',
            ],
        ]);

        // Every school widget, in a logical order with an alternating
        // background rhythm (hero/flush = full-bleed, navy = dark, alt = soft grey).
        $blocks = [
            ['flush',   'announcement-bar'],
            ['hero',    'hero'],
            ['flush',   'quick-links'],
            ['section', 'leadership'],
            ['navy',    'stats'],
            ['section', 'academic-programs'],
            ['alt',     'facilities'],
            ['section', 'campus'],
            ['alt',     'admission-process'],
            ['section', 'parent-testimonials'],
            ['alt',     'achievements'],
            ['section', 'news-calendar'],
            ['alt',     'life'],
            ['section', 'glimpses'],
            ['alt',     'videos'],
            ['section', 'map'],
            ['flush',   'admission-cta'],
            ['flush',   'floating-action'],
        ];

        $sections = [];
        foreach ($blocks as [$type, $widget]) {
            $sections[] = [
                'type' => $type,
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [['type' => $widget, 'settings' => []]],
                    ]],
                ]],
            ];
        }

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);
    }
}
