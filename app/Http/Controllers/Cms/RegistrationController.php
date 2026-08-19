<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Enquiry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Handles standalone online student registration form & submission storage.
 */
class RegistrationController extends Controller
{
    /**
     * Display standalone Online Registration Form page.
     */
    public function show(): View
    {
        return view('admissions.register');
    }

    /**
     * Store submitted student registration data.
     */
    public function store(Request $request): RedirectResponse
    {
        // ── 1. Honeypot check for spam bots ────────────────────────────────
        if ($request->filled('website')) {
            return back();
        }

        // ── 2. Input Validation ──────────────────────────────────────────
        $validated = $request->validate([
            'applying_for'    => 'required|string|max:50',
            'full_name'       => 'required|string|max:255',
            'dob_day'         => 'nullable|numeric|between:1,31',
            'dob_month'       => 'nullable|numeric|between:1,12',
            'dob_year'        => 'nullable|numeric|min:1990|max:' . date('Y'),
            'gender'          => 'required|string|in:Male,Female,Other',
            'previous_school' => 'nullable|string|max:255',
            'father_name'     => 'required|string|max:255',
            'mother_name'     => 'required|string|max:255',
            'mobile'          => 'required|string|max:15',
            'address'         => 'nullable|string|max:500',
            'email'           => 'nullable|email|max:255',
        ]);

        // ── 3. Format Date of Birth ───────────────────────────────────────
        $dob = null;
        if (!empty($validated['dob_year']) && !empty($validated['dob_month']) && !empty($validated['dob_day'])) {
            $dob = sprintf('%04d-%02d-%02d', $validated['dob_year'], $validated['dob_month'], $validated['dob_day']);
        }

        // ── 4. Build Meta & Subject ───────────────────────────────────────
        $studentName = $validated['full_name'];
        $fatherName  = $validated['father_name'];
        $motherName  = $validated['mother_name'];
        $class       = $validated['applying_for'];
        $mobile      = $validated['mobile'];
        $email       = $validated['email'] ?? ($mobile . '@registration.prayaagschool.com');

        $meta = [
            'student_name'    => $studentName,
            'father_name'     => $fatherName,
            'mother_name'     => $motherName,
            'gender'          => $validated['gender'],
            'dob'             => $dob,
            'class_applying'  => $class,
            'previous_school' => $validated['previous_school'] ?? null,
            'address'         => $validated['address'] ?? null,
        ];

        // ── 5. Create Enquiry Record ─────────────────────────────────────
        $enquiry = Enquiry::create([
            'type'    => 'admission',
            'name'    => $fatherName . ' (Father of ' . $studentName . ')',
            'email'   => $email,
            'phone'   => $mobile,
            'subject' => 'Online Registration – ' . $studentName . ' (' . $class . ')',
            'message' => "Online Student Registration:\nStudent: {$studentName}\nClass: {$class}\nFather: {$fatherName}\nMother: {$motherName}\nPhone: {$mobile}\nAddress: " . ($validated['address'] ?? 'N/A'),
            'source'  => 'online_registration_form',
            'status'  => 'new',
            'meta'    => $meta,
        ]);

        // ── 6. Admin Notification ─────────────────────────────────────────
        AdminNotification::record('lead', 'New Registration: ' . $studentName . ' (' . $class . ')', [
            'body' => "Parent: {$fatherName} · Phone: {$mobile} · Class: {$class}",
            'url'  => url('/admin/leads'),
            'icon' => 'user-plus',
        ]);

        // ── 7. Send Email Alert if configured ────────────────────────────
        try {
            /** @var \App\Core\Mail\MailManager $mailManager */
            $mailManager = app(\App\Core\Mail\MailManager::class);
            $adminAlertEmail = config('mail.enquiry_alert_email', config('mail.from.address', 'admissions@prayaagschool.com'));
            $mailManager->send('enquiry_admin_alert', [
                'name'    => $studentName,
                'email'   => $email,
                'phone'   => $mobile,
                'message' => "New Registration for Class {$class}. Father: {$fatherName}, Mother: {$motherName}.",
                'type'    => 'Online Registration',
            ], $adminAlertEmail);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Registration email alert error: " . $e->getMessage());
        }

        return back()->with('registration_sent', true)->with('success', 'Registration form for ' . $studentName . ' submitted successfully!');
    }
}
