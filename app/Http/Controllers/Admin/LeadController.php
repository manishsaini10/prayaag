<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\View\View;

/**
 * Admission leads — public enquiries submitted with type=admission. Reuses the
 * enquiry status workflow (admin.enquiries.status) for follow-up tracking.
 */
class LeadController extends Controller
{
    public function index(): View
    {
        $leads = Enquiry::where('type', 'admission')->latest()->paginate(20);

        $stats = [
            'total' => Enquiry::where('type', 'admission')->count(),
            'new'   => Enquiry::where('type', 'admission')->where('status', 'new')->count(),
        ];

        return view('admin.leads.index', compact('leads', 'stats'));
    }
}
