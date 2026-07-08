<?php

namespace Database\Seeders;

use App\Core\Settings\SettingsManager;
use App\Models\SettingGroup;
use Illuminate\Database\Seeder;

/**
 * Seeds the setting groups (UI sections) and a starter set of values.
 * Run after Phase2Seeder.
 */
class Phase3Seeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['name' => 'General', 'slug' => 'general', 'sort_order' => 1],
            ['name' => 'SEO',     'slug' => 'seo',     'sort_order' => 2],
            ['name' => 'Contact', 'slug' => 'contact', 'sort_order' => 3],
        ];

        foreach ($groups as $group) {
            SettingGroup::firstOrCreate(['slug' => $group['slug']], $group);
        }

        $settings = app(SettingsManager::class);

        $settings->set('site_name', 'Demo School', 'string', 'general');
        $settings->set('site_tagline', 'Excellence in Education', 'string', 'general');
        $settings->set('maintenance_mode', false, 'boolean', 'general');
        $settings->set('posts_per_page', 10, 'integer', 'general');
        $settings->set('meta_description', 'Welcome to Demo School', 'string', 'seo');
        $settings->set('contact_email', 'info@school.test', 'string', 'contact');
    }
}
