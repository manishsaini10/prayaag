<?php

namespace App\Services;

use App\Models\AcademicSession;
use App\Models\AcademicCalendarEntry;
use Barryvdh\DomPDF\Facade\Pdf;

class AcademicCalendarPdfExporter
{
    public function export(AcademicSession $session)
    {
        $entries = AcademicCalendarEntry::with(['term', 'class'])
            ->where('session_id', $session->id)
            ->where('status', 'published')
            ->orderBy('start_date', 'asc')
            ->get();

        // Group entries by month
        $groupedEntries = $entries->groupBy(function ($entry) {
            return $entry->start_date->format('F Y');
        });

        // Group terms for term overview section
        $terms = $session->terms()->orderBy('start_date', 'asc')->get();

        $pdf = Pdf::loadView('pdf.academic-calendar', [
            'session'        => $session,
            'groupedEntries' => $groupedEntries,
            'terms'          => $terms,
            'entries'        => $entries,
        ]);

        return $pdf->download("Academic_Calendar_{$session->session_name}.pdf");
    }
}
