<?php

namespace App\Core\Theme;

use App\Core\Menu\MenuManager;
use App\Core\Settings\SettingsManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

/**
 * Renders the site chrome (header + footer) as Blade partials, driven entirely
 * by the Settings Engine and the Menu Builder — no hardcoded content. The
 * partials live in resources/views/themes/school/partials/. Chrome is rendered
 * fresh per request (one menu query; settings come from cache), keeping it
 * always in sync with admin changes.
 */
class ThemeRenderer
{
    public function __construct(
        protected SettingsManager $settings,
        protected MenuManager $menus,
    ) {
    }

    public function header(): string
    {
        // Cached forever; invalidated on settings/menu changes (see flush()).
        // The footer is intentionally NOT cached because it embeds a per-request
        // CSRF token (newsletter form).
        return Cache::rememberForever('theme.header', fn () => View::make('themes.school.partials.header', $this->chrome([
            'menu' => $this->menus->tree('primary'),
        ]))->render());
    }

    public function footer(): string
    {
        return View::make('themes.school.partials.footer', $this->chrome([
            'menu'       => $this->menus->tree('primary'),
            'footerMenu' => $this->menus->tree('footer'),
        ]))->render();
    }

    /** Invalidate cached chrome. Called on settings + menu mutations. */
    public static function flush(): void
    {
        Cache::forget('theme.header');
    }

    /**
     * Extra <head> markup driven by Theme settings: custom web fonts and
     * CSS-variable overrides for fonts + colors. Emitted AFTER the default
     * stylesheet so these win. Returns '' when nothing is customised.
     */
    public function themeHead(): string
    {
        $s = $this->settings;
        $clean = fn (string $v): string => trim(str_replace(['<', '>', '{', '}'], '', $v));
        $out = '';

        // Uploaded custom font → register an @font-face the theme can use.
        $cfFamily = $clean((string) $s->get('theme_custom_font_family', ''));
        $cfUrl    = $clean((string) $s->get('theme_custom_font_url', ''));
        if ($cfFamily !== '' && $cfUrl !== '') {
            $ext = strtolower(pathinfo(parse_url($cfUrl, PHP_URL_PATH) ?: $cfUrl, PATHINFO_EXTENSION));
            $fmt = ['woff2' => 'woff2', 'woff' => 'woff', 'ttf' => 'truetype', 'otf' => 'opentype'][$ext] ?? 'woff2';
            $out .= '<style>@font-face{font-family:"' . e($cfFamily) . '";'
                . 'src:url("' . e($cfUrl) . '") format("' . $fmt . '");'
                . 'font-display:swap;}</style>';
        }

        // Custom web fonts: paste a full Google Fonts <link> URL in settings.
        $fontsUrl = $clean((string) $s->get('theme_google_fonts_url', ''));
        if ($fontsUrl !== '' && str_starts_with($fontsUrl, 'https://')) {
            $out .= '<link href="' . e($fontsUrl) . '" rel="stylesheet">';
        }

        // CSS variable overrides — only emit the ones that are actually set.
        $vars = [];
        if (($f = $clean((string) $s->get('theme_heading_font', ''))) !== '') {
            $vars[] = '--font-head:' . $f;
        }
        if (($f = $clean((string) $s->get('theme_body_font', ''))) !== '') {
            $vars[] = '--font-body:' . $f;
        }
        if (($c = $clean((string) $s->get('theme_color_navy', ''))) !== '') {
            $vars[] = '--navy:' . $c;
            $vars[] = '--bg-navy:' . $c;
        }
        if (($c = $clean((string) $s->get('theme_color_navy3', ''))) !== '') {
            $vars[] = '--navy-3:' . $c;
        }
        if (($c = $clean((string) $s->get('theme_color_gold', ''))) !== '') {
            $vars[] = '--gold:' . $c;
        }
        if (($c = $clean((string) $s->get('theme_color_gold2', ''))) !== '') {
            $vars[] = '--gold-2:' . $c;
        }
        if (($c = $clean((string) $s->get('theme_primary_color', ''))) !== '') {
            $vars[] = '--primary:' . $c;
        }

        // Menu styling overrides (WordPress-like theme customizer)
        $menuKeys = [
            'menu_font_size'      => '--menu-font-size',
            'menu_font_weight'    => '--menu-font-weight',
            'menu_color'          => '--menu-color',
            'menu_color_hover'    => '--menu-color-hover',
            'menu_color_active'   => '--menu-color-active',
            'menu_bg'             => '--menu-bg',
            'menu_bg_hover'       => '--menu-bg-hover',
            'menu_bg_active'      => '--menu-bg-active',
            'menu_padding'        => '--menu-padding',
            'menu_border_radius'  => '--menu-border-radius',
            'submenu_bg'          => '--submenu-bg',
            'submenu_color'       => '--submenu-color',
            'submenu_bg_hover'    => '--submenu-bg-hover',
            'submenu_color_hover' => '--submenu-color-hover',
        ];
        foreach ($menuKeys as $key => $cssVar) {
            $v = $clean((string) $s->get($key, ''));
            if ($v !== '') {
                $vars[] = $cssVar . ':' . $v;
            }
        }

        // Header button styling
        $btnKeys = [
            'cta_bg'             => '--cta-bg',
            'cta_color'          => '--cta-color',
            'cta_bg_hover'       => '--cta-bg-hover',
            'cta_border_radius'  => '--cta-border-radius',
            'top_pay_bg'         => '--top-pay-bg',
            'top_pay_color'      => '--top-pay-color',
            'top_link_color'     => '--top-link-color',
        ];
        foreach ($btnKeys as $key => $cssVar) {
            $v = $clean((string) $s->get($key, ''));
            if ($v !== '') {
                $vars[] = $cssVar . ':' . $v;
            }
        }

        if ($vars) {
            $out .= '<style>:root{' . implode(';', $vars) . ';}</style>';
        }

        return $out;
    }

    /** JSON-LD structured data (EducationalOrganization + WebSite) from settings. */
    public function schema(): string
    {
        $s = $this->settings;
        $name = $s->get('site_name', 'Prayaag International School');
        $url = url('/');

        $sameAs = array_values(array_filter([
            $s->get('social_facebook'),
            $s->get('social_instagram'),
            $s->get('social_twitter'),
            $s->get('social_linkedin'),
            $s->get('social_youtube'),
        ]));

        $org = array_filter([
            '@type'     => 'EducationalOrganization',
            '@id'       => $url . '#org',
            'name'      => $name,
            'url'       => $url,
            'logo'      => $s->get('site_logo') ?: null,
            'telephone' => $s->get('contact_phone') ?: null,
            'address'   => ($addr = $s->get('contact_address'))
                ? ['@type' => 'PostalAddress', 'streetAddress' => $addr, 'addressCountry' => 'IN']
                : null,
            'sameAs'    => $sameAs ?: null,
        ], fn ($v) => $v !== null && $v !== '');

        $graph = [
            '@context' => 'https://schema.org',
            '@graph'   => [
                $org,
                ['@type' => 'WebSite', '@id' => $url . '#website', 'name' => $name, 'url' => $url],
            ],
        ];

        return '<script type="application/ld+json">'
            . json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            . '</script>';
    }

    /** Shared chrome data (settings + an inline-SVG icon resolver). */
    protected function chrome(array $extra = []): array
    {
        $s = $this->settings;

        return array_merge([
            'siteName' => $s->get('site_name', 'Prayaag International School'),
            'tagline'  => $s->get('site_tagline', 'Life begins here.'),
            'about'    => $s->get('site_about'),
            'logo'     => $s->get('site_logo', 'https://prayaaginternationalschool.com/wp-content/uploads/2021/12/prayaag-school-logo.png'),
            'email'    => $s->get('contact_email'),
            'phone'    => $s->get('contact_phone'),
            'address'  => $s->get('contact_address'),
            'mapEmbed' => $s->get('google_map_embed'),
            'ctaLabel' => $s->get('admission_cta_label', 'Apply Now'),
            'ctaUrl'   => $s->get('admission_cta_url', '/admissions'),
            'topNotes' => array_values(array_filter([
                $s->get('top_note_1', 'CBSE Affiliation No. : 531592'),
                $s->get('top_note_2', 'School Code : 41568'),
            ])),
            'topLinks' => array_values(array_filter([
                ['label' => 'Student Login', 'url' => $s->get('student_login_url', 'http://prayaag.accevate.com/'), 'ic' => 'user', 'style' => 'link'],
                ['label' => 'Admin Login', 'url' => $s->get('admin_login_url', 'http://prayaag.accevate.com/admin/'), 'ic' => 'lock', 'style' => 'link'],
                ['label' => 'Pay Online', 'url' => $s->get('online_payment_url', 'https://pisp.accevate.com/online/main'), 'ic' => 'card', 'style' => 'btn'],
            ], fn ($l) => ! empty($l['url']))),
            'badge'    => $s->get('header_badge_image', ''),
            'social'   => [
                'facebook'  => $s->get('social_facebook'),
                'instagram' => $s->get('social_instagram'),
                'twitter'   => $s->get('social_twitter'),
                'linkedin'  => $s->get('social_linkedin'),
                'youtube'   => $s->get('social_youtube'),
            ],
            // Header Settings (Theme Builder). All boolean toggles default to a
            // sensible "on" except glass/transparent. 'header_variant' picks one
            // of the 5 premium layouts: hv-01 … hv-05.
            'hVariant'     => $s->get('header_variant', 'hv-01'),
            'fVariant'     => $s->get('footer_variant', 'fv-01'),
            'hSticky'      => (bool) $s->get('header_sticky', true),
            'hTopbar'      => (bool) $s->get('header_topbar', true),
            'hSocial'      => (bool) $s->get('header_social', true),
            'hSearch'      => (bool) $s->get('header_search', false),
            'hCta'         => (bool) $s->get('header_cta', true),
            'hLogin'       => (bool) $s->get('header_login', true),
            'hGlass'       => (bool) $s->get('header_glass', false),
            'hTransparent' => (bool) $s->get('header_transparent', false),
            'icon'     => fn (string $name): string => self::ICONS[$name] ?? '',
        ], $extra);
    }

    /** Inline SVGs (currentColor) so the chrome needs no icon font/asset. */
    protected const ICONS = [
        'shield'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
        'building'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4"/><path d="M9 9h.01M9 12h.01M9 15h.01M9 18h.01"/></svg>',
        'user'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
        'lock'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>',
        'card'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>',
        'chat'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
        'search'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>',
        'phone'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
        'mail'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></svg>',
        'pin'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>',
        'facebook'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.78-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0 0 22 12z"/></svg>',
        'instagram' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1.2" fill="currentColor" stroke="none"/></svg>',
        'youtube'   => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M23 12s0-3.5-.46-5.17a2.78 2.78 0 0 0-1.95-1.96C18.88 4.4 12 4.4 12 4.4s-6.88 0-8.59.47A2.78 2.78 0 0 0 1.46 6.83 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.17 2.78 2.78 0 0 0 1.95 1.96c1.71.47 8.59.47 8.59.47s6.88 0 8.59-.47a2.78 2.78 0 0 0 1.95-1.96C23 15.5 23 12 23 12zM9.75 15.02V8.98L15.5 12z"/></svg>',
        'twitter'   => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
        'linkedin'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.13 1.45-2.13 2.94v5.67H9.35V9h3.42v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.45v6.29zM5.34 7.43a2.06 2.06 0 1 1 0-4.13 2.06 2.06 0 0 1 0 4.13zM7.12 20.45H3.55V9h3.57v11.45zM22.22 0H1.77C.8 0 0 .77 0 1.73v20.54C0 23.22.8 24 1.77 24h20.45c.98 0 1.78-.78 1.78-1.73V1.73C24 .77 23.2 0 22.22 0z"/></svg>',
    ];
}
