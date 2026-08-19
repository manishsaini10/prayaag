<?php

namespace App\Http\Controllers;

use App\Core\Mess\Services\MessMenuService;
use App\Core\Seo\SeoManager;
use App\Core\Settings\SettingsManager;
use App\Core\Theme\ThemeRenderer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MessMenuController extends Controller
{
    public function __construct(
        protected MessMenuService $service,
        protected ThemeRenderer $theme,
        protected SettingsManager $settings,
    ) {}

    /**
     * Public-facing Mess Menu page.
     */
    public function index(Request $request)
    {
        $data     = $this->service->getActiveMenuGrouped();
        $menu     = $data['menu']     ?? null;
        $grouped  = $data['grouped']  ?? [];
        $schedule = $data['schedule'] ?? [];

        // Today's special overrides if any
        $specialOverrides = $menu
            ? $this->service->getSpecialOverrideForDate(now()->toDateString())
            : [];

        $seoData = app(SeoManager::class)->resolve(
            title: ($menu->title ?? 'Weekly Mess Menu') . ' — Prayaag International School',
            slug:  'mess-menu'
        );

        $content = view('widgets.mess-menu', compact('menu', 'grouped', 'schedule', 'specialOverrides'))->render();

        return view('themes.school.layout', [
            'title'        => $seoData->title,
            'siteName'     => $seoData->siteName,
            'content'      => $content,
            'header'       => $this->theme->header(),
            'footer'       => $this->theme->footer(),
            'themeHead'    => $this->theme->themeHead(),
            'primaryColor' => $this->settings->get('theme_primary_color', '#0b2545'),
            'seo'          => $seoData->toArray(),
        ]);
    }

    /**
     * Download the active weekly mess menu as a PDF.
     */
    public function downloadPdf(Request $request)
    {
        $data = $this->service->getActiveMenuGrouped();

        $menu     = $data['menu']     ?? null;
        $grouped  = $data['grouped']  ?? [];
        $schedule = $data['schedule'] ?? [];

        if (! $menu) {
            abort(404, 'No active mess menu found.');
        }

        $specialOverrides = [];

        $pdf = Pdf::loadView('pdf.mess-menu', compact('menu', 'grouped', 'schedule', 'specialOverrides'))
            ->setPaper('A4', 'portrait');

        $filename = 'Mess_Menu_' . $menu->effective_from->format('d_M_Y') . '.pdf';

        return $pdf->download($filename);
    }
}
