<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\SettingGroup;
use Illuminate\Database\Seeder;

/**
 * Seeds the "Theme" settings group: header/footer layout selection (one-click),
 * custom web fonts, and custom brand colors. Read by ThemeRenderer.
 *
 *   header_variant          hv-01 | hv-02 | hv-03 | hv-04 | hv-05
 *   footer_variant          fv-01 | fv-02 | fv-03 | fv-04 | fv-05
 *   theme_google_fonts_url  full <link> URL from fonts.google.com (optional)
 *   theme_heading_font      CSS font-family for headings (e.g. "Merriweather, serif")
 *   theme_body_font         CSS font-family for body text
 *   theme_primary_color     hex (buttons / accents)
 *   theme_color_navy        hex (primary brand / dark)
 *   theme_color_navy3       hex (secondary navy)
 *   theme_color_gold        hex (gold accent)
 *   theme_color_gold2       hex (light gold)
 *
 * Menu styling (applied as CSS variables — like a WordPress theme customizer):
 *   menu_font_size          e.g. ".92rem"
 *   menu_font_weight        e.g. "600"
 *   menu_color              hex / CSS color
 *   menu_color_hover        hex
 *   menu_color_active       hex
 *   menu_bg                 hex / CSS color
 *   menu_bg_hover           hex
 *   menu_bg_active          hex
 *   menu_padding            e.g. ".55rem .9rem"
 *   menu_border_radius      e.g. "7px"
 *   submenu_bg              hex
 *   submenu_color           hex
 *   submenu_bg_hover        hex
 *   submenu_color_hover     hex
 *
 * Header button styling:
 *   cta_bg                  hex (CTA "Apply Now" background)
 *   cta_color               hex (CTA text color)
 *   cta_bg_hover            hex (CTA hover background)
 *   cta_border_radius       e.g. "8px"
 *   top_pay_bg              hex ("Pay Online" background)
 *   top_pay_color           hex ("Pay Online" text color)
 *   top_link_color          hex (top bar login links color)
 */
class ThemeSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $group = SettingGroup::firstOrCreate(
            ['name' => 'Theme'],
            ['description' => 'Header & footer layout, fonts, colors (one-click)', 'sort_order' => 4]
        );

        $defs = [
            ['key' => 'header_variant', 'value' => 'hv-01', 'type' => 'text'],
            ['key' => 'footer_variant', 'value' => 'fv-01', 'type' => 'text'],
            ['key' => 'theme_google_fonts_url', 'value' => '', 'type' => 'text'],
            ['key' => 'theme_heading_font', 'value' => '', 'type' => 'text'],
            ['key' => 'theme_body_font', 'value' => '', 'type' => 'text'],
            ['key' => 'theme_primary_color', 'value' => '', 'type' => 'text'],
            ['key' => 'theme_color_navy', 'value' => '', 'type' => 'text'],
            ['key' => 'theme_color_navy3', 'value' => '', 'type' => 'text'],
            ['key' => 'theme_color_gold', 'value' => '', 'type' => 'text'],
            ['key' => 'theme_color_gold2', 'value' => '', 'type' => 'text'],

            // Menu styling (WordPress-like theme customizer)
            ['key' => 'menu_font_size', 'value' => '', 'type' => 'text'],
            ['key' => 'menu_font_weight', 'value' => '', 'type' => 'text'],
            ['key' => 'menu_color', 'value' => '', 'type' => 'text'],
            ['key' => 'menu_color_hover', 'value' => '', 'type' => 'text'],
            ['key' => 'menu_color_active', 'value' => '', 'type' => 'text'],
            ['key' => 'menu_bg', 'value' => '', 'type' => 'text'],
            ['key' => 'menu_bg_hover', 'value' => '', 'type' => 'text'],
            ['key' => 'menu_bg_active', 'value' => '', 'type' => 'text'],
            ['key' => 'menu_padding', 'value' => '', 'type' => 'text'],
            ['key' => 'menu_border_radius', 'value' => '', 'type' => 'text'],
            ['key' => 'submenu_bg', 'value' => '', 'type' => 'text'],
            ['key' => 'submenu_color', 'value' => '', 'type' => 'text'],
            ['key' => 'submenu_bg_hover', 'value' => '', 'type' => 'text'],
            ['key' => 'submenu_color_hover', 'value' => '', 'type' => 'text'],

            // Header button styling
            ['key' => 'cta_bg', 'value' => '', 'type' => 'text'],
            ['key' => 'cta_color', 'value' => '', 'type' => 'text'],
            ['key' => 'cta_bg_hover', 'value' => '', 'type' => 'text'],
            ['key' => 'cta_border_radius', 'value' => '', 'type' => 'text'],
            ['key' => 'top_pay_bg', 'value' => '', 'type' => 'text'],
            ['key' => 'top_pay_color', 'value' => '', 'type' => 'text'],
            ['key' => 'top_link_color', 'value' => '', 'type' => 'text'],
        ];

        foreach ($defs as $d) {
            $setting = Setting::firstOrCreate(
                ['key' => $d['key']],
                ['value' => $d['value'], 'type' => $d['type'], 'group_id' => $group->id]
            );
            // Ensure these all live under the Theme tab (header_variant may have
            // been seeded earlier under the Header group).
            if ($setting->group_id !== $group->id) {
                $setting->update(['group_id' => $group->id]);
            }
        }
    }
}
