<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\SettingGroup;
use Illuminate\Database\Seeder;

/**
 * Global SEO defaults consumed by SeoManager. These power the auto-generation
 * fallback chain so no page ever ships empty metadata.
 *
 *   seo_title_template       e.g. "{title} | {site}"
 *   seo_default_title        home / fallback <title>
 *   seo_default_description  fallback meta description
 *   seo_default_keywords     fallback meta keywords
 *   seo_default_og_image     fallback social share image (absolute URL)
 *   seo_twitter_handle       e.g. "@prayaagschool"
 *   seo_locale               og:locale (default en_IN)
 *
 *   php artisan db:seed --class=Database\\Seeders\\SeoSettingsSeeder
 */
class SeoSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $group = SettingGroup::firstOrCreate(
            ['name' => 'SEO'],
            ['description' => 'Global SEO defaults (titles, social, fallbacks)', 'sort_order' => 5]
        );

        $defs = [
            ['key' => 'seo_title_template',      'value' => '{title} | {site}', 'type' => 'text'],
            ['key' => 'seo_default_title',       'value' => '', 'type' => 'text'],
            ['key' => 'seo_default_description', 'value' => '', 'type' => 'text'],
            ['key' => 'seo_default_keywords',    'value' => '', 'type' => 'text'],
            ['key' => 'seo_default_og_image',    'value' => '', 'type' => 'text'],
            ['key' => 'seo_twitter_handle',      'value' => '', 'type' => 'text'],
            ['key' => 'seo_locale',              'value' => 'en_IN', 'type' => 'text'],
            ['key' => 'seo_schema_org_type',     'value' => 'EducationalOrganization', 'type' => 'text'],
            ['key' => 'seo_indexnow_key',         'value' => '', 'type' => 'text'],
            ['key' => 'seo_robots_custom',        'value' => '', 'type' => 'text'],
        ];

        foreach ($defs as $d) {
            $setting = Setting::firstOrCreate(
                ['key' => $d['key']],
                ['value' => $d['value'], 'type' => $d['type'], 'group_id' => $group->id]
            );
            if ($setting->group_id !== $group->id) {
                $setting->update(['group_id' => $group->id]);
            }
        }
    }
}
