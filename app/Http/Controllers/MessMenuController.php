<?php

namespace App\Http\Controllers;

use App\Core\Mess\Services\MessMenuService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MessMenuController extends Controller
{
    public function __construct(protected MessMenuService $service) {}

    /**
     * Download the active weekly mess menu as a PDF.
     */
    public function downloadPdf(Request $request)
    {
        $data = $this->service->getActiveMenuGrouped();

        $menu    = $data['menu']    ?? null;
        $grouped = $data['grouped'] ?? [];

        if (! $menu) {
            abort(404, 'No active mess menu found.');
        }

        // Special overrides for today (passed as empty for PDF; the PDF view reads grouped directly)
        $specialOverrides = [];

        $pdf = Pdf::loadView('pdf.mess-menu', compact('menu', 'grouped', 'specialOverrides'))
            ->setPaper('A4', 'portrait');

        $filename = 'Mess_Menu_' . $menu->effective_from->format('d_M_Y') . '.pdf';

        return $pdf->download($filename);
    }
}
