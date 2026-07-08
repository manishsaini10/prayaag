<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Receives public contact/enquiry submissions. Honeypot + route-level
 * throttle guard against bots.
 */
class EnquiryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        // Honeypot: bots fill the hidden "website" field. Silently drop them.
        if ($request->filled('website')) {
            return back();
        }

        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
            'type'    => 'nullable|string|in:contact,admission,enquiry',
            'source'  => 'nullable|string|max:255',
        ]);

        Enquiry::create([
            'type'    => $data['type'] ?? 'contact',
            'name'    => $data['name'],
            'email'   => $data['email'],
            'phone'   => $data['phone'] ?? null,
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'],
            'source'  => $data['source'] ?? null,
            'status'  => 'new',
        ]);

        return back()->with('enquiry_sent', true);
    }
}
