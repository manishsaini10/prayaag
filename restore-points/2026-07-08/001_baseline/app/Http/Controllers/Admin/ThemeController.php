<?php

namespace App\Http\Controllers\Admin;

use App\Core\Settings\SettingsManager;
use App\Core\Theme\ThemeRenderer;
use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Advanced Theme Builder: visual header/footer pickers, brand colors, fonts
 * (Google Fonts + custom font upload). Writes to the Settings Engine and
 * flushes caches so changes apply to the live site immediately.
 */
class ThemeController extends Controller
{
    /** Boolean header toggles managed by this screen. */
    protected array $toggles = [
        'header_sticky', 'header_topbar', 'header_social',
        'header_cta', 'header_login', 'header_glass', 'header_transparent',
    ];

    /** Color settings (key => label). */
    protected array $colors = [
        'theme_primary_color' => 'Primary / Accent',
        'theme_color_navy'    => 'Brand Dark (Navy)',
        'theme_color_navy3'   => 'Secondary Navy',
        'theme_color_gold'    => 'Gold Accent',
        'theme_color_gold2'   => 'Light Gold',
    ];

    /** Menu text styling keys (key => label). */
    protected array $menuText = [
        'menu_font_size'     => 'Font size',
        'menu_font_weight'   => 'Font weight',
        'menu_padding'       => 'Item padding',
        'menu_border_radius' => 'Border radius',
    ];

    /** Button text styling keys (key => label). */
    protected array $btnText = [
        'cta_border_radius' => 'CTA border radius',
    ];

    /** Button color styling keys (key => label). */
    protected array $btnColors = [
        'cta_bg'        => 'CTA background',
        'cta_color'     => 'CTA text color',
        'cta_bg_hover'  => 'CTA hover background',
        'top_pay_bg'    => 'Pay Online background',
        'top_pay_color' => 'Pay Online text color',
        'top_link_color' => 'Top links color',
    ];

    /** Menu color styling keys (key => label). */
    protected array $menuColors = [
        'menu_color'          => 'Text color',
        'menu_color_hover'    => 'Text color (hover)',
        'menu_color_active'   => 'Text color (active)',
        'menu_bg'             => 'Background',
        'menu_bg_hover'       => 'Background (hover)',
        'menu_bg_active'      => 'Background (active)',
        'submenu_bg'          => 'Submenu background',
        'submenu_color'       => 'Submenu text color',
        'submenu_bg_hover'    => 'Submenu hover background',
        'submenu_color_hover' => 'Submenu hover text color',
    ];

    /** Default hex shown in the pickers when a color isn't customised. */
    protected array $colorDefaults = [
        'theme_primary_color' => '#0b2545',
        'theme_color_navy'    => '#0b2545',
        'theme_color_navy3'   => '#1c3a6e',
        'theme_color_gold'    => '#c79a3b',
        'theme_color_gold2'   => '#e0b94e',
        'menu_color'          => '#18202f',
        'menu_color_hover'    => '#1c3a6e',
        'menu_color_active'   => '#1d4ed8',
        'menu_bg'             => '#ffffff',
        'menu_bg_hover'       => '#f6f8fc',
        'menu_bg_active'      => '#e8f0fb',
        'submenu_bg'          => '#ffffff',
        'submenu_color'       => '#3b4658',
        'submenu_bg_hover'    => '#f6f8fc',
        'submenu_color_hover' => '#1c3a6e',
        'cta_bg'              => '#2563eb',
        'cta_color'           => '#ffffff',
        'cta_bg_hover'        => '#1d4ed8',
        'top_pay_bg'          => '#22c55e',
        'top_pay_color'       => '#ffffff',
        'top_link_color'      => '#18202f',
    ];

    public function index(SettingsManager $settings): View
    {
        $headers = [
            'hv-01' => ['name' => 'Modern Clean Corporate', 'desc' => 'White, crisp, blue accents'],
            'hv-02' => ['name' => 'Glassmorphism Floating', 'desc' => 'Frosted, rounded, floating card'],
            'hv-03' => ['name' => 'Luxury Premium', 'desc' => 'Navy + gold, elegant dark'],
            'hv-04' => ['name' => 'Enterprise Mega Menu', 'desc' => 'Structured nav band, mega columns'],
            'hv-05' => ['name' => 'Next.js / Vercel Minimal', 'desc' => 'Monochrome, single-row, tight'],
        ];
        $footers = [
            'fv-01' => ['name' => 'Classic Navy', 'desc' => '4-column premium (default)'],
            'fv-02' => ['name' => 'Centered Minimal', 'desc' => 'Single column, centered'],
            'fv-03' => ['name' => 'Light', 'desc' => 'Soft grey, dark text'],
            'fv-04' => ['name' => 'Gradient Accent', 'desc' => 'Navy → blue gradient'],
            'fv-05' => ['name' => 'Compact', 'desc' => 'Brand + links only, slim'],
        ];

        $current = [
            'header_variant' => $settings->get('header_variant', 'hv-01'),
            'footer_variant' => $settings->get('footer_variant', 'fv-01'),
            'heading_font'   => $settings->get('theme_heading_font', ''),
            'body_font'      => $settings->get('theme_body_font', ''),
            'google_fonts'   => $settings->get('theme_google_fonts_url', ''),
            'custom_family'  => $settings->get('theme_custom_font_family', ''),
            'custom_url'     => $settings->get('theme_custom_font_url', ''),
        ];
        foreach ($this->toggles as $t) {
            $default = $t === 'header_glass' || $t === 'header_transparent' ? false : true;
            $current[$t] = (bool) $settings->get($t, $default);
        }
        foreach ($this->colors as $key => $label) {
            $current[$key] = $settings->get($key, '') ?: $this->colorDefaults[$key];
        }
        // Menu text settings
        foreach ($this->menuText as $key => $label) {
            $current[$key] = $settings->get($key, '');
        }
        // Menu color settings
        foreach ($this->menuColors as $key => $label) {
            $current[$key] = $settings->get($key, '') ?: ($this->colorDefaults[$key] ?? '');
        }
        // Button text settings
        foreach ($this->btnText as $key => $label) {
            $current[$key] = $settings->get($key, '');
        }
        // Button color settings
        foreach ($this->btnColors as $key => $label) {
            $current[$key] = $settings->get($key, '') ?: ($this->colorDefaults[$key] ?? '');
        }

        return view('admin.theme.index', [
            'headers'       => $headers,
            'footers'       => $footers,
            'current'       => $current,
            'colors'        => $this->colors,
            'colorDefaults' => $this->colorDefaults,
            'menuText'      => $this->menuText,
            'menuColors'    => $this->menuColors,
            'btnText'       => $this->btnText,
            'btnColors'     => $this->btnColors,
        ]);
    }

    public function save(Request $request, SettingsManager $settings): RedirectResponse
    {
        // Layout pickers
        $settings->set('header_variant', $this->oneOf($request->input('header_variant'), array_keys([
            'hv-01' => 1, 'hv-02' => 1, 'hv-03' => 1, 'hv-04' => 1, 'hv-05' => 1,
        ]), 'hv-01'), 'string');
        $settings->set('footer_variant', $this->oneOf($request->input('footer_variant'), [
            'fv-01', 'fv-02', 'fv-03', 'fv-04', 'fv-05',
        ], 'fv-01'), 'string');

        // Toggles
        foreach ($this->toggles as $t) {
            $settings->set($t, $request->boolean($t), 'boolean');
        }

        // Colors (store '' to fall back to default; validate hex)
        foreach (array_keys($this->colors) as $key) {
            $val = trim((string) $request->input($key, ''));
            $settings->set($key, $this->hexOrEmpty($val), 'string');
        }

        // Fonts
        $settings->set('theme_heading_font', trim((string) $request->input('theme_heading_font', '')), 'string');
        $settings->set('theme_body_font', trim((string) $request->input('theme_body_font', '')), 'string');
        $settings->set('theme_google_fonts_url', trim((string) $request->input('theme_google_fonts_url', '')), 'string');

        // Menu text settings
        foreach ($this->menuText as $key => $label) {
            $val = trim((string) $request->input($key, ''));
            $settings->set($key, $val, 'string');
        }
        // Menu color settings
        foreach ($this->menuColors as $key => $label) {
            $val = trim((string) $request->input($key, ''));
            $settings->set($key, $val, 'string');
        }
        // Button text settings
        foreach ($this->btnText as $key => $label) {
            $val = trim((string) $request->input($key, ''));
            $settings->set($key, $val, 'string');
        }
        // Button color settings
        foreach ($this->btnColors as $key => $label) {
            $val = trim((string) $request->input($key, ''));
            $settings->set($key, $val, 'string');
        }

        $this->bust();

        return back()->with('status', 'Theme saved and applied to the live site.');
    }

    /** Upload a custom font file → store on public disk → register @font-face. */
    public function uploadFont(Request $request, SettingsManager $settings): RedirectResponse
    {
        $request->validate([
            'font'        => 'required|file|max:5120|mimetypes:font/woff2,font/woff,font/ttf,font/otf,application/octet-stream,application/font-woff,application/x-font-ttf,application/vnd.ms-opentype',
            'font_family' => 'nullable|string|max:60',
        ]);

        $file = $request->file('font');
        $ext = strtolower($file->getClientOriginalExtension());
        if (! in_array($ext, ['woff2', 'woff', 'ttf', 'otf'], true)) {
            return back()->withErrors(['font' => 'Please upload a .woff2, .woff, .ttf or .otf file.']);
        }

        $filename = (string) Str::ulid() . '.' . $ext;
        $path = $file->storeAs('fonts', $filename, 'public');

        rescue(fn () => Media::create([
            'disk'          => 'public',
            'path'          => $path,
            'filename'      => $filename,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'extension'     => $ext,
            'size'          => $file->getSize(),
        ]), null, false);

        $family = trim((string) $request->input('font_family', ''))
            ?: Str::title(str_replace(['-', '_'], ' ', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)));

        $settings->set('theme_custom_font_url', Storage::disk('public')->url($path), 'string');
        $settings->set('theme_custom_font_family', $family, 'string');

        $this->bust();

        return back()->with('status', 'Custom font "' . $family . '" uploaded. Set it as your Heading or Body font below.');
    }

    /** Remove the uploaded custom font reference. */
    public function removeFont(SettingsManager $settings): RedirectResponse
    {
        $settings->set('theme_custom_font_url', '', 'string');
        $settings->set('theme_custom_font_family', '', 'string');
        $this->bust();

        return back()->with('status', 'Custom font removed.');
    }

    protected function oneOf(?string $value, array $allowed, string $fallback): string
    {
        return in_array($value, $allowed, true) ? $value : $fallback;
    }

    protected function hexOrEmpty(string $val): string
    {
        return preg_match('/^#[0-9a-fA-F]{3,8}$/', $val) ? $val : '';
    }

    protected function bust(): void
    {
        ThemeRenderer::flush();
        rescue(fn () => \Illuminate\Support\Facades\Cache::flush(), null, false);
    }
}
