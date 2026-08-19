<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Services\RecaptchaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Receives public contact/enquiry submissions.
 *
 * Security layers (Phase 1):
 *  1. Honeypot field ("website") — silently drops bots.
 *  2. reCAPTCHA v3 token verification.
 *  3. Route-level throttle:10,1 in routes/web.php.
 */
class EnquiryController extends Controller
{
    public function store(Request $request, RecaptchaService $recaptcha): RedirectResponse
    {
        // ── 1. Honeypot: bots fill the hidden "website" field. ─────────────
        if ($request->filled('website')) {
            return back();
        }

        // ── 2. reCAPTCHA v3 ───────────────────────────────────────────────
        if (! $recaptcha->verify($request->input('g-recaptcha-response'), 'enquiry')) {
            return back()
                ->withInput()
                ->withErrors(['captcha' => 'We could not verify you are human. Please try again.']);
        }

        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|max:255',
            'phone'           => 'nullable|string|max:50',
            'subject'         => 'nullable|string|max:255',
            'message'         => 'nullable|string|max:5000',
            'type'            => 'nullable|string|in:contact,admission,enquiry',
            'source'          => 'nullable|string|max:255',
            // Admission-specific extra fields (stored in meta JSON)
            'student_name'    => 'nullable|string|max:255',
            'gender'          => 'nullable|string|in:male,female,other',
            'dob'             => 'nullable|date',
            'class_applying'  => 'nullable|string|max:50',
            'previous_school' => 'nullable|string|max:255',
            'address'         => 'nullable|string|max:500',
        ]);

        // Build meta for admission-specific fields
        $meta = null;
        if ($data['type'] === 'admission') {
            $meta = array_filter([
                'student_name'    => $data['student_name']    ?? null,
                'gender'          => $data['gender']          ?? null,
                'dob'             => $data['dob']             ?? null,
                'class_applying'  => $data['class_applying']  ?? null,
                'previous_school' => $data['previous_school'] ?? null,
                'address'         => $data['address']         ?? null,
            ]);
        }

        // Use student_name as subject for admin readability
        $subject = $data['subject'] ?? null;
        if ($data['type'] === 'admission' && !$subject && !empty($data['class_applying'])) {
            $subject = 'Admission – ' . $data['class_applying'];
        }

        $enquiry = Enquiry::create([
            'type'    => $data['type'] ?? 'contact',
            'name'    => $data['name'],
            'email'   => $data['email'],
            'phone'   => $data['phone'] ?? null,
            'subject' => $subject,
            'message' => $data['message'] ?? null,
            'source'  => $data['source'] ?? null,
            'status'  => 'new',
            'meta'    => $meta ?: null,
        ]);

        try {
            /** @var \App\Core\Mail\MailManager $mailManager */
            $mailManager = app(\App\Core\Mail\MailManager::class);
            $mailManager->send('enquiry_auto_reply', [
                'name' => $enquiry->name,
                'message' => $enquiry->message ?? 'N/A',
            ], $enquiry->email);

            $adminAlertEmail = config('mail.enquiry_alert_email', config('mail.from.address', 'admissions@prayaag.edu.in'));
            $mailManager->send('enquiry_admin_alert', [
                'name' => $enquiry->name,
                'email' => $enquiry->email,
                'phone' => $enquiry->phone ?? 'N/A',
                'message' => $enquiry->message ?? 'N/A',
                'type' => ucfirst($enquiry->type),
            ], $adminAlertEmail);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Failed to trigger enquiry emails: " . $e->getMessage());
        }

        return back()->with('enquiry_sent', true);
    }
}
