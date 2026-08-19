<?php

namespace App\Http\Controllers;

use App\Core\Video\VideoManager;
use App\Events\NewVideoTestimonialSubmission;
use App\Jobs\ProcessVideoUploadJob;
use App\Models\AdminNotification;
use App\Models\VideoTestimonial;
use App\Services\MimeVerifier;
use App\Services\RecaptchaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Handles public video testimonial submissions.
 *
 * Security layers (Phase 1):
 *  1. reCAPTCHA v3 token verification.
 *  2. Magic-byte MIME verification for video uploads via MimeVerifier.
 *  3. ClamAV malware scan stub (enabled via CLAMAV_ENABLED=true).
 *  4. Route-level throttle:5,1 in routes/web.php.
 */
class VideoTestimonialSubmissionController extends Controller
{
    public function __construct(private VideoManager $videoManager) {}

    public function show(): View
    {
        return view('site.video-testimonials.submit');
    }

    public function store(
        Request          $request,
        RecaptchaService $recaptcha,
        MimeVerifier     $mimeVerifier
    ): RedirectResponse {
        // ── 1. reCAPTCHA v3 ───────────────────────────────────────────────
        if (! $recaptcha->verify($request->input('g-recaptcha-response'), 'video_testimonial')) {
            return back()
                ->withInput()
                ->withErrors(['captcha' => 'We could not verify you are human. Please try again.']);
        }

        $validated = $request->validate([
            'student_name'       => 'required|string|max:150',
            'class_grade'        => 'nullable|string|max:50',
            'submitted_by_name'  => 'required|string|max:150',
            'submitted_by_email' => 'required|email|max:150',
            'submitted_by_phone' => 'nullable|string|max:30',
            'title'              => 'required|string|max:255',
            'video_url'          => 'nullable|url|max:500',
            // Extension + size only — magic-byte check is done below.
            'video_file'         => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/webm|max:' . (config('video.max_upload_mb', 250) * 1024),
            'consent_confirmed'  => 'required|accepted',
            'consent_signed_by'  => 'required|string|max:150',
        ]);

        // Must have either a URL or a file
        if (empty($validated['video_url']) && $request->missing('video_file')) {
            return back()->withInput()->withErrors(['video_url' => 'Please provide a YouTube link or upload a video file.']);
        }

        // ── 2. Magic-byte MIME check + ClamAV scan for video file uploads ──
        if ($request->hasFile('video_file')) {
            $error = $mimeVerifier->validate(
                $request->file('video_file'),
                array_keys(MimeVerifier::ALLOWED_VIDEO_MIMES)
            );

            if ($error !== null) {
                return back()->withInput()->withErrors(['video_file' => $error]);
            }
        }

        try {
            if ($request->hasFile('video_file')) {
                // Store file temporarily; actual provider upload happens in the queue job
                $tempPath = $request->file('video_file')->store('temp-video-uploads', 'local');

                $video = VideoTestimonial::create([
                    'title'               => $validated['title'],
                    'student_name'        => $validated['student_name'],
                    'class_grade'         => $validated['class_grade'] ?? null,
                    'submitted_by_name'   => $validated['submitted_by_name'],
                    'submitted_by_email'  => $validated['submitted_by_email'],
                    'submitted_by_phone'  => $validated['submitted_by_phone'] ?? null,
                    'video_provider'      => config('video.default_provider'),
                    'video_external_id'   => 'pending_' . uniqid(),
                    'status'              => 'pending',
                    'consent_confirmed'   => true,
                    'consent_signed_by'   => $validated['consent_signed_by'],
                    'consent_signed_at'   => now(),
                ]);

                // Dispatch queued job — never blocks the HTTP request
                ProcessVideoUploadJob::dispatch($video->id, $tempPath, config('video.default_provider'));

            } else {
                // URL import — resolve synchronously (fast, no file upload)
                $provider = $this->videoManager->driver();
                $result   = $provider->upload($validated['video_url'], ['title' => $validated['title']]);

                $video = VideoTestimonial::create([
                    'title'               => $validated['title'],
                    'student_name'        => $validated['student_name'],
                    'class_grade'         => $validated['class_grade'] ?? null,
                    'submitted_by_name'   => $validated['submitted_by_name'],
                    'submitted_by_email'  => $validated['submitted_by_email'],
                    'submitted_by_phone'  => $validated['submitted_by_phone'] ?? null,
                    'video_provider'      => $provider->key(),
                    'video_external_id'   => $result->id,
                    'video_embed_url'     => $result->embedUrl,
                    'thumbnail_url'       => $result->thumbnailUrl,
                    'duration_seconds'    => $result->durationSeconds,
                    'status'              => 'pending', // always enters moderation — never auto-publishes
                    'consent_confirmed'   => true,
                    'consent_signed_by'   => $validated['consent_signed_by'],
                    'consent_signed_at'   => now(),
                ]);
            }

            NewVideoTestimonialSubmission::dispatch($video);

            try {
                /** @var \App\Core\Mail\MailManager $mailManager */
                $mailManager = app(\App\Core\Mail\MailManager::class);
                $mailManager->send('video_testimonial_submitted_confirmation', [
                    'submitter_name' => $validated['submitted_by_name'],
                    'video_title' => $validated['title'],
                ], $validated['submitted_by_email']);

                $adminEmail = config('mail.from.address', 'admin@prayaag.edu.in');
                $mailManager->send('video_testimonial_admin_moderation_alert', [
                    'title' => $validated['title'],
                    'submitter_name' => $validated['submitted_by_name'],
                    'admin_review_url' => url('/admin/video-testimonials?status=pending'),
                ], $adminEmail);
            } catch (\Throwable $e) {
                Log::error('Failed to trigger video testimonial emails', ['error' => $e->getMessage()]);
            }

            AdminNotification::record('video_testimonial', "New video testimonial submitted by {$validated['submitted_by_name']}", [
                'body' => "Student: {$validated['student_name']} — awaiting moderation",
                'url'  => url('/admin/video-testimonials?status=pending'),
                'icon' => 'video-camera',
            ]);

            return redirect()->back()->with('success',
                'Thank you! Your video has been submitted and is under review. We will publish it once approved.'
            );

        } catch (\Throwable $e) {
            Log::error('Public video testimonial submission failed', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Something went wrong. Please try again or contact us directly.');
        }
    }
}
