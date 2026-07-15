<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class TestimonialPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'post-testimonial'],
            ['title' => 'Post Your Testimonial', 'status' => 'published']
        );

        $page->update([
            'status' => 'published',
            'seo' => [
                'title' => 'Post Your Testimonial | Prayaag International School',
                'description' => 'Share your feedback and experience with the Prayaag International School community.',
            ],
        ]);

        $sections = [
            [
                'type' => 'flush',
                'rows' => [
                    [
                        'columns' => [
                            [
                                'width' => 12,
                                'widgets' => [
                                    [
                                        'type' => 'testimonial_page',
                                        'settings' => [],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Post Testimonial page created with TestimonialPageWidget.');
    }
}
