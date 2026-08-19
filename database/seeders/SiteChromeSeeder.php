<?php

namespace Database\Seeders;

use App\Core\Settings\SettingsManager;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\SettingGroup;
use Illuminate\Database\Seeder;

/**
 * Seeds the real Prayaag International School chrome data into the CMS so the
 * dynamic theme header/footer render the exact same content as the original
 * static home page — contact, social, logo, map, SEO, and the primary/footer
 * menus. Idempotent: re-running refreshes values and rebuilds the menus.
 *
 * Run: php artisan db:seed --class=Database\\Seeders\\SiteChromeSeeder
 */
class SiteChromeSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->seedPrimaryMenu();
        $this->seedFooterMenu();
    }

    protected function seedSettings(): void
    {
        foreach ([
            ['name' => 'General', 'slug' => 'general', 'sort_order' => 1],
            ['name' => 'SEO',     'slug' => 'seo',     'sort_order' => 2],
            ['name' => 'Contact', 'slug' => 'contact', 'sort_order' => 3],
            ['name' => 'Social',  'slug' => 'social', 'sort_order' => 4],
            ['name' => 'Header',  'slug' => 'header', 'sort_order' => 5],
            ['name' => 'Theme',   'slug' => 'theme',  'sort_order' => 6],
        ] as $group) {
            SettingGroup::firstOrCreate(['slug' => $group['slug']], $group);
        }

        $img = 'https://prayaaginternationalschool.com/wp-content/uploads/';
        $s = app(SettingsManager::class);

        // General / brand
        $s->set('site_name', 'Prayaag International School', 'string', 'general');
        $s->set('site_tagline', 'Life begins here.', 'string', 'general');
        $s->set('site_about', 'A CBSE-affiliated school in Panipat nurturing young minds and shaping the leaders of tomorrow through academic excellence, creativity and strong values.', 'string', 'general');
        $s->set('site_logo', $img . '2021/12/prayaag-school-logo.png', 'string', 'general');
        $s->set('og_image', $img . '2022/01/About-Prayaag-International-School.webp', 'string', 'seo');

        // SEO
        $s->set('meta_description', 'Top School in Panipat 2025-26. Best CBSE Affiliated Play/Preschool, Secondary and Senior Sec. Schools in Panipat. Top Schools in Samalkha.', 'string', 'seo');
        $s->set('seo_robots_noindex', false, 'boolean', 'seo');

        // Contact
        $s->set('contact_email', '', 'string', 'contact');
        $s->set('contact_phone', '+91 93507 48851', 'string', 'contact');
        $s->set('contact_address', 'Prayaag International School, Opp. New Police Lines, Near Indraprastha Institute of Medical Sciences, NH-44, Panipat-132103, Haryana', 'string', 'contact');
        $s->set('google_map_embed', '<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d13914.976184424926!2d76.986936!3d29.3191828!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xd337897af9217763!2sPrayaag%20International%20School%2C%20Panipat!5e0!3m2!1sen!2sin!4v1640849540342!5m2!1sen!2sin" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>', 'string', 'contact');

        // Admission CTA (header button)
        $s->set('admission_cta_label', 'Apply Now', 'string', 'header');
        $s->set('admission_cta_url', '/registration', 'string', 'header');

        // Header utility bar (top strip) — matches the original site
        $s->set('top_note_1', 'CBSE Affiliation No. : 531592', 'string', 'header');
        $s->set('top_note_2', 'School Code : 41568', 'string', 'header');
        $s->set('student_login_url', 'http://prayaag.accevate.com/', 'string', 'header');
        $s->set('admin_login_url', 'http://prayaag.accevate.com/admin/', 'string', 'header');
        $s->set('online_payment_url', 'https://pisp.accevate.com/online/main', 'string', 'header');
        $s->set('header_badge_image', 'https://prayaaginternationalschool.com/wp-content/uploads/2022/01/british-council-logo-150x150.jpg', 'string', 'header');

        // Social
        $s->set('social_facebook', 'https://www.facebook.com/PrayaagInternationalSchoolPanipat', 'string', 'social');
        $s->set('social_instagram', 'http://instagram.com/prayaag2016', 'string', 'social');
        $s->set('social_twitter', 'https://twitter.com/MailusIntl', 'string', 'social');
        $s->set('social_linkedin', 'https://www.linkedin.com/company/prayaag-international-school', 'string', 'social');
        $s->set('social_youtube', 'https://www.youtube.com/channel/UCeqR_-8SsGfMi09aX1FSzdA', 'string', 'social');

        // Theme
        $s->set('theme_primary_color', '#0b2545', 'string', 'theme');
    }

    protected function seedPrimaryMenu(): void
    {
        $items = [
            ['Home', '/'],
            ['About Us', '/about-us/'],
            ['Admission Process', '/admissions/', [
                ['Admissions', '/admissions/'],
                ['Fee Structure', '/fee-structure/'],
            ]],
            ['Our Campus', '#', [
                ['Junior Wing', '/junior-wing-school-in-panipat/'],
                ['Senior Wing', '/senior-wing-school-in-panipat/'],
            ]],
            ['Facilities', '/classrooms/', [
                ['Classrooms', '/classrooms/'],
                ['Labs', '/labs/'],
                ['Library', '/library/'],
                ['Sports', '/sports/'],
                ['Transportations', '/transportations/'],
                ['Safety & Security', '/safety-security/'],
                ['Tours and Excursions', '/tours-and-excursions/'],
                ['UNESCO', '/unesco/'],
            ]],
            ['Events', '/events/'],
            ['Alumni', '/alumni/'],
            ['Academic Downloads', '/downloads/'],
            ['Media', '/media/'],
            ['Mandatory Public Disclosure', '#', [
                ['Fee Structure', '/storage/pdfs/Fee_Structure_2026-27.pdf'],
                ['Transport Fee', '/storage/pdfs/Transport_Fee_Structure_2026-27.pdf'],
                ['Mandatory Public Disclosure', '/storage/pdfs/Mandatory_Public_Disclosure.pdf'],
                ['Building Safety Certificate', '/storage/pdfs/BSC.pdf'],
                ['Transport Safety Certificate', '/storage/pdfs/TSC.pdf'],
                ['Fire Safety Certificate', '/storage/pdfs/FSC.pdf'],
            ]],
            ['Contact Us', '/contact-us/'],
        ];

        $this->buildMenu('Primary Menu', 'primary', $items);
    }

    protected function seedFooterMenu(): void
    {
        $items = [
            ['Top Schools in Panipat', '/top-10-schools-in-panipat/'],
            ['Best Schools in Samalkha', '/best-schools-in-samalkha/'],
            ['Best Pre Nursery School', '/best-pre-nursery-school-in-panipat/'],
            ['Disclosure', '/disclosure/'],
            ['Book List', '/book-list/'],
            ['Career', '/career/'],
            ['Media', '/media/'],
        ];

        $this->buildMenu('Footer Links', 'footer', $items);
    }

    /**
     * Create (or refresh) a menu at a location and (re)build its items.
     *
     * @param  array<int, array{0:string,1:string,2?:array<int,array{0:string,1:string}>}>  $items
     */
    protected function buildMenu(string $name, string $location, array $items): void
    {
        $menu = Menu::firstOrCreate(
            ['location' => $location],
            ['name' => $name, 'slug' => $location]
        );

        // Idempotent rebuild.
        MenuItem::where('menu_id', $menu->id)->delete();

        $order = 0;
        foreach ($items as $item) {
            [$label, $url] = [$item[0], $item[1]];
            $children = $item[2] ?? [];

            $parent = MenuItem::create([
                'menu_id'    => $menu->id,
                'parent_id'  => null,
                'label'      => $label,
                'type'       => 'url',
                'url'        => $url,
                'sort_order' => $order++,
            ]);

            $childOrder = 0;
            foreach ($children as [$clabel, $curl]) {
                MenuItem::create([
                    'menu_id'    => $menu->id,
                    'parent_id'  => $parent->id,
                    'label'      => $clabel,
                    'type'       => 'url',
                    'url'        => $curl,
                    'sort_order' => $childOrder++,
                ]);
            }
        }
    }
}
