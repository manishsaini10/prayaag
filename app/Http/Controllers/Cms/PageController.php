<?php

namespace App\Http\Controllers\Cms;

use App\Core\Analytics\PageViewRecorder;
use App\Core\Builder\PageRenderer;
use App\Core\Seo\SchemaBuilder;
use App\Core\Seo\SeoManager;
use App\Core\Settings\SettingsManager;
use App\Core\Theme\ThemeRenderer;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;

/**
 * Serves built pages on the public front-end (single-site). Pages are looked
 * up by slug; only published pages are served.
 */
class PageController extends Controller
{
    public function home(
        PageRenderer $renderer,
        ThemeRenderer $theme,
        SettingsManager $settings,
    ): View {
        return $this->renderPage('home', $renderer, $theme, $settings);
    }

    public function show(
        string $slug,
        PageRenderer $renderer,
        ThemeRenderer $theme,
        SettingsManager $settings,
    ): View {
        return $this->renderPage($slug, $renderer, $theme, $settings);
    }

    protected function renderPage(
        string $slug,
        PageRenderer $renderer,
        ThemeRenderer $theme,
        SettingsManager $settings,
    ): View {
        $page = Page::published()->where('slug', $slug)->firstOrFail();

        // First-party analytics. Wrapped so it can never break rendering.
        rescue(fn () => app(PageViewRecorder::class)->record(request(), $page), null, false);

        // Render page content safely – if rendering fails, fall back to a placeholder
        $content = rescue(fn () => $renderer->renderCached($page), '', false);
        if (empty($content)) {
            Log::warning('PageRenderer returned empty content for slug: ' . $slug);
            $content = view('themes.school.partials.empty-page')->render();
        }
        $isHome = $slug === 'home';
        $seoData = app(SeoManager::class)->forPage($page, $content, $isHome);
        $seo = $seoData->toArray();

        return view('themes.school.layout', [
            'title'        => $seo['title'],
            'siteName'     => $seo['site_name'],
            'content'      => $content,
            'header'       => $theme->header(),
            'footer'       => $theme->footer(),
            'themeHead'    => $theme->themeHead(),
            'schema'       => app(SchemaBuilder::class)->forPage($page, $seoData, $isHome),
            'primaryColor' => $settings->get('theme_primary_color', '#0b2545'),
            'seo'          => $seo,
        ]);
    }
}
