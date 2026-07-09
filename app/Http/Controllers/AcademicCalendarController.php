<?php

namespace App\Http\Controllers;

use App\Core\Seo\SeoManager;
use App\Core\Settings\SettingsManager;
use App\Core\Theme\ThemeRenderer;
use App\Http\Resources\AcademicCalendarEntryResource;
use App\Models\AcademicCalendarEntry;
use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use App\Services\AcademicCalendarPdfExporter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicCalendarController extends Controller
{
    public function __construct(
        protected ThemeRenderer $theme,
        protected SettingsManager $settings
    ) {
    }

    public function index(Request $request): View
    {
        $currentSession = AcademicSession::where('is_current', true)->first()
            ?? AcademicSession::orderBy('start_date', 'desc')->first();

        $selectedSessionId = $request->input('session_id', $currentSession ? $currentSession->id : null);
        $selectedTermId = $request->input('term_id');

        $sessions = AcademicSession::orderBy('start_date', 'desc')->get();
        $terms = $selectedSessionId
            ? AcademicTerm::where('session_id', $selectedSessionId)->orderBy('start_date', 'asc')->get()
            : collect();

        // SEO configuration
        $seoData = app(SeoManager::class)->resolve(
            title: 'Academic Calendar',
            slug: 'academic-calendar'
        );

        // Render main content inside standard site layout
        return view('themes.school.layout', [
            'title'        => $seoData->title,
            'siteName'     => $seoData->siteName,
            'content'      => view('academic-calendar.index', compact('sessions', 'terms', 'selectedSessionId', 'selectedTermId', 'currentSession'))->render(),
            'header'       => $this->theme->header(),
            'footer'       => $this->theme->footer(),
            'themeHead'    => $this->theme->themeHead(),
            'primaryColor' => $this->settings->get('theme_primary_color', '#0b2545'),
            'seo'          => $seoData->toArray(),
        ]);
    }

    /**
     * JSON Feed endpoint for FullCalendar.
     */
    public function feed(Request $request): JsonResponse
    {
        $sessionId = $request->input('session_id');
        $termId = $request->input('term_id');

        if (!$sessionId) {
            $currentSession = AcademicSession::where('is_current', true)->first()
                ?? AcademicSession::orderBy('start_date', 'desc')->first();
            $sessionId = $currentSession ? $currentSession->id : null;
        }

        if (!$sessionId) {
            return response()->json([]);
        }

        $query = AcademicCalendarEntry::with(['session', 'term', 'class'])
            ->where('session_id', $sessionId)
            ->where('status', 'published');

        if ($termId) {
            $query->where('term_id', $termId);
        }

        $entries = $query->get();

        return response()->json(AcademicCalendarEntryResource::collection($entries));
    }

    /**
     * PDF Export of the full session calendar.
     */
    public function exportPdf(Request $request, AcademicCalendarPdfExporter $exporter)
    {
        $sessionId = $request->input('session_id');

        if (!$sessionId) {
            $session = AcademicSession::where('is_current', true)->first()
                ?? AcademicSession::orderBy('start_date', 'desc')->first();
        } else {
            $session = AcademicSession::findOrFail($sessionId);
        }

        if (!$session) {
            return back()->with('error', 'Academic Session not found.');
        }

        return $exporter->export($session);
    }
}
