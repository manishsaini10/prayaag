<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Admission leads — public enquiries submitted with type=admission.
 * Supports Lead management, status updates, CSV export, and printable PDF report generation.
 */
class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $query = Enquiry::where('type', 'admission');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('subject', 'like', "%{$s}%");
            });
        }

        $leads = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total'    => Enquiry::where('type', 'admission')->count(),
            'new'      => Enquiry::where('type', 'admission')->where('status', 'new')->count(),
            'read'     => Enquiry::where('type', 'admission')->where('status', 'read')->count(),
            'archived' => Enquiry::where('type', 'admission')->where('status', 'archived')->count(),
        ];

        return view('admin.leads.index', compact('leads', 'stats'));
    }

    /**
     * Export admission leads to CSV file format.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $filename = 'admission_leads_' . date('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($request) {
            $handle = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fputs($handle, "\xEF\xBB\xBF");

            // CSV Header Row
            fputcsv($handle, [
                'Lead ID',
                'Received Date',
                'Parent Name',
                'Email',
                'Phone',
                'Subject',
                'Student Name',
                'Gender',
                'DOB',
                'Class Applying',
                'Previous School',
                'Address',
                'Message',
                'Status',
            ]);

            $query = Enquiry::where('type', 'admission');

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('name', 'like', "%{$s}%")
                      ->orWhere('email', 'like', "%{$s}%")
                      ->orWhere('phone', 'like', "%{$s}%")
                      ->orWhere('subject', 'like', "%{$s}%");
                });
            }

            $query->latest()->chunk(200, function ($leads) use ($handle) {
                foreach ($leads as $lead) {
                    $meta = $lead->meta ?? [];
                    fputcsv($handle, [
                        $lead->id,
                        $lead->created_at ? $lead->created_at->format('Y-m-d H:i:s') : '',
                        $lead->name,
                        $lead->email,
                        $lead->phone ?? '',
                        $lead->subject ?? '',
                        $meta['student_name'] ?? '',
                        $meta['gender'] ?? '',
                        $meta['dob'] ?? '',
                        $meta['class_applying'] ?? '',
                        $meta['previous_school'] ?? '',
                        $meta['address'] ?? '',
                        $lead->message ?? '',
                        ucfirst($lead->status),
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate printable PDF report view for admission leads.
     */
    public function exportPdf(Request $request): View
    {
        $query = Enquiry::where('type', 'admission');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('subject', 'like', "%{$s}%");
            });
        }

        $leads = $query->latest()->get();

        $stats = [
            'total'    => Enquiry::where('type', 'admission')->count(),
            'new'      => Enquiry::where('type', 'admission')->where('status', 'new')->count(),
            'read'     => Enquiry::where('type', 'admission')->where('status', 'read')->count(),
            'archived' => Enquiry::where('type', 'admission')->where('status', 'archived')->count(),
        ];

        return view('admin.leads.pdf', compact('leads', 'stats'));
    }
}
