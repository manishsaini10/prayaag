<?php

namespace App\Http\Controllers\Cms;

use App\Core\Media\MediaManager;
use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Services\MimeVerifier;
use App\Services\RecaptchaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Receives public job applications. The target listing must be open.
 * Optional résumé is stored privately via the media library.
 *
 * Security layers (Phase 1):
 *  1. Honeypot field ("website") — silently drops bots.
 *  2. reCAPTCHA v3 token verification via RecaptchaService.
 *  3. Magic-byte MIME verification via MimeVerifier (not extension/client MIME).
 *  4. ClamAV malware scan (stub — enabled via CLAMAV_ENABLED=true).
 *  5. Route-level throttle:5,1 applied in routes/web.php.
 */
class JobApplicationController extends Controller
{
    public function store(
        Request         $request,
        MediaManager    $media,
        RecaptchaService $recaptcha,
        MimeVerifier    $mimeVerifier
    ): RedirectResponse {
        // ── 1. Honeypot: bots fill the hidden "website" field. ──────────────
        if ($request->filled('website')) {
            return back();
        }

        // ── 2. reCAPTCHA v3 ─────────────────────────────────────────────────
        if (! $recaptcha->verify($request->input('g-recaptcha-response'), 'job_apply')) {
            return back()
                ->withInput()
                ->withErrors(['captcha' => 'We could not verify you are human. Please try again.']);
        }

        // ── 3. Validate form fields ──────────────────────────────────────────
        $data = $request->validate([
            'job_listing_id' => 'required|string',
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'phone'          => 'nullable|string|max:50',
            'cover_letter'   => 'nullable|string|max:5000',
            // Extension + size only (magic-byte check done below for real content validation).
            'resume'         => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        // Must be an open listing, else 404.
        $job = JobListing::open()->findOrFail($data['job_listing_id']);

        $resumeId = null;
        if ($request->hasFile('resume')) {
            // ── 4. Magic-byte MIME check + ClamAV scan ───────────────────────
            $error = $mimeVerifier->validate(
                $request->file('resume'),
                array_keys(MimeVerifier::ALLOWED_DOCUMENT_MIMES)
            );

            if ($error !== null) {
                return back()->withInput()->withErrors(['resume' => $error]);
            }

            // Private disk: résumés contain PII and must not be publicly fetchable.
            // Served only via the gated admin download route (/admin/applications/{id}/resume).
            $resumeId = $media->store($request->file('resume'), null, 'local')->id;
        }

        $app = JobApplication::create([
            'job_listing_id'  => $job->id,
            'name'            => $data['name'],
            'email'           => $data['email'],
            'phone'           => $data['phone'] ?? null,
            'cover_letter'    => $data['cover_letter'] ?? null,
            'resume_media_id' => $resumeId,
            'status'          => 'new',
        ]);

        try {
            /** @var \App\Core\Mail\MailManager $mailManager */
            $mailManager = app(\App\Core\Mail\MailManager::class);
            $mailManager->send('job_application_received', [
                'candidate_name' => $app->name,
                'job_title' => $job->title,
            ], $app->email);

            $adminAlertEmail = config('mail.hr_alert_email', config('mail.from.address', 'hr@prayaag.edu.in'));
            $mailManager->send('job_application_admin_alert', [
                'candidate_name' => $app->name,
                'candidate_email' => $app->email,
                'job_title' => $job->title,
                'admin_application_url' => url('/admin/applications'),
            ], $adminAlertEmail);

            $app->update(['email_notification_sent_at' => now()]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Failed to trigger job application emails: " . $e->getMessage());
        }

        return back()->with('application_sent', true);
    }
}
