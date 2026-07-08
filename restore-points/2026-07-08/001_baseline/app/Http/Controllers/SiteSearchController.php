<?php

namespace App\Http\Controllers;

use App\Core\Settings\SettingsManager;
use App\Core\Theme\ThemeRenderer;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Public site search. Searches published pages by title and renders branded
 * results inside the theme layout (the "search template").
 */
class SiteSearchController extends Controller
{
    public function index(Request $request, ThemeRenderer $theme, SettingsManager $settings): View
    {
        $q = trim((string) $request->query('q', ''));

        $results = collect();
        if ($q !== '') {
            $results = Page::published()
                ->where('title', 'like', '%' . $q . '%')
                ->orderBy('title')
                ->limit(40)
                ->get(['title', 'slug']);
        }

        $content = view('themes.school.partials.search-results', compact('q', 'results'))->render();

        return view('themes.school.layout', [
            'title'        => $q !== '' ? ('Search: ' . $q) : 'Search',
            'siteName'     => $settings->get('site_name', 'Prayaag International School'),
            'content'      => $content,
            'header'       => $theme->header(),
            'footer'       => $theme->footer(),
            'schema'       => $theme->schema(),
            'primaryColor' => $settings->get('theme_primary_color', '#0b2545'),
            'seo'          => ['description' => 'Search the site.'],
        ]);
    }
}
