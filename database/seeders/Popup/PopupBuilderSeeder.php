<?php

namespace Database\Seeders\Popup;

use App\Core\Popup\Services\TemplateService;
use App\Models\Popup\Popup;
use App\Models\Popup\PopupCategory;
use App\Models\Popup\PopupLead;
use App\Models\Popup\PopupRule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PopupBuilderSeeder extends Seeder
{
    public function run(TemplateService $templateService): void
    {
        // Seed categories
        $categories = [
            ['name' => 'Marketing', 'color' => '#6366f1', 'sort_order' => 1],
            ['name' => 'School Notices', 'color' => '#f59e0b', 'sort_order' => 2],
            ['name' => 'Admissions', 'color' => '#10b981', 'sort_order' => 3],
            ['name' => 'Events', 'color' => '#ef4444', 'sort_order' => 4],
            ['name' => 'Emergency', 'color' => '#dc2626', 'sort_order' => 5],
            ['name' => 'Compliance', 'color' => '#8b5cf6', 'sort_order' => 6],
            ['name' => 'Newsletter', 'color' => '#06b6d4', 'sort_order' => 7],
            ['name' => 'Seasonal', 'color' => '#ec4899', 'sort_order' => 8],
        ];

        foreach ($categories as $cat) {
            PopupCategory::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                array_merge($cat, ['slug' => Str::slug($cat['name'])])
            );
        }

        // Seed built-in templates
        $templateService->seedDefaults();

        // Create sample popups
        $samplePopups = [
            [
                'title' => 'Welcome to Our School',
                'type' => 'welcome',
                'status' => 'draft',
                'structure' => [
                    'container' => ['type' => 'container', 'styles' => ['padding' => '40', 'background' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', 'borderRadius' => '16']],
                    'rows' => [
                        ['columns' => [['width' => 12, 'widgets' => [
                            ['type' => 'heading', 'content' => 'Welcome to Prayaag International School', 'settings' => ['tag' => 'h2', 'align' => 'center', 'color' => '#ffffff', 'fontSize' => '28']],
                            ['type' => 'paragraph', 'content' => 'Discover excellence in education. Apply for the 2026-27 academic session.', 'settings' => ['align' => 'center', 'color' => '#e2e8f0', 'fontSize' => '16']],
                            ['type' => 'button', 'content' => 'Explore Admissions', 'settings' => ['align' => 'center', 'backgroundColor' => '#f59e0b', 'textColor' => '#ffffff', 'borderRadius' => '50', 'padding' => '14 32']],
                        ]]]]
                    ]
                ],
                'settings' => ['width' => '550', 'overlay' => true, 'close_button' => true, 'animation' => 'zoom'],
            ],
            [
                'title' => 'Newsletter Signup',
                'type' => 'newsletter',
                'status' => 'draft',
                'structure' => [
                    'container' => ['type' => 'container', 'styles' => ['padding' => '35', 'background' => '#ffffff', 'borderRadius' => '12', 'boxShadow' => '0 10px 40px rgba(0,0,0,0.1)']],
                    'rows' => [
                        ['columns' => [['width' => 12, 'widgets' => [
                            ['type' => 'heading', 'content' => 'Stay Updated!', 'settings' => ['tag' => 'h3', 'align' => 'center', 'color' => '#1e293b']],
                            ['type' => 'paragraph', 'content' => 'Get the latest school news, events, and updates directly in your inbox.', 'settings' => ['align' => 'center', 'color' => '#64748b']],
                            ['type' => 'newsletter_form', 'settings' => ['buttonText' => 'Subscribe', 'buttonColor' => '#6366f1', 'placeholder' => 'Enter your email address']],
                        ]]]]
                    ]
                ],
                'settings' => ['width' => '480', 'overlay' => true, 'close_button' => true, 'animation' => 'fade'],
            ],
        ];

        $cat = PopupCategory::first();
        foreach ($samplePopups as $data) {
            $slug = Str::slug($data['title']) . '-' . uniqid();
            Popup::firstOrCreate(
                ['slug' => $slug],
                array_merge($data, [
                    'slug' => $slug,
                    'category_id' => $cat?->id,
                    'frequency_type' => 'once_per_session',
                ])
            );
        }

        // Sample leads
        if (PopupLead::count() === 0 && ($popup = Popup::first())) {
            PopupLead::factory(10)->create(['popup_id' => $popup->id]);
        }

        $this->command->info('Popup Builder seeded: categories, templates, sample popups, and leads.');
    }
}
