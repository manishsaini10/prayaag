<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\JobApplication;
use App\Models\Subscriber;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Admin views + actions over the captured data (single-site). Reads and
 * route-model-bound writes operate across the whole site.
 */
class InboxController extends Controller
{
    // --- Enquiries ---

    public function enquiries(): View
    {
        $enquiries = Enquiry::latest()->limit(200)->get();

        return view('admin.enquiries', ['enquiries' => $enquiries]);
    }

    public function updateEnquiryStatus(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $data = $request->validate(['status' => 'required|in:new,read,archived']);
        $enquiry->update(['status' => $data['status']]);

        return back();
    }

    // --- Job applications ---

    public function applications(): View
    {
        $applications = JobApplication::with(['jobListing', 'resume'])->latest()->limit(200)->get();

        return view('admin.applications', ['applications' => $applications]);
    }

    public function updateApplicationStatus(Request $request, JobApplication $application): RedirectResponse
    {
        $data = $request->validate(['status' => 'required|in:new,reviewing,rejected,hired']);
        $application->update(['status' => $data['status']]);

        try {
            app(\App\Core\Mail\MailManager::class)->send('job_application_status_changed', [
                'candidate_name' => $application->name,
                'job_title' => $application->jobListing->title ?? 'Position',
                'status' => ucfirst($data['status']),
            ], $application->email);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send job application status change email: " . $e->getMessage());
        }

        return back();
    }

    /** Gated résumé download. Résumés live on the private disk and are reachable only through this authenticated admin route. */
    public function downloadResume(JobApplication $application): StreamedResponse
    {
        $media = $application->resume;
        abort_unless($media, 404);

        $disk = Storage::disk($media->disk);
        abort_unless($disk->exists($media->path), 404);

        return $disk->download($media->path, $media->original_name ?? 'resume');
    }

    // --- Subscribers ---

    public function subscribers(): View
    {
        $subscribers = Subscriber::latest()->limit(500)->get();

        return view('admin.subscribers', ['subscribers' => $subscribers]);
    }

    public function unsubscribe(Subscriber $subscriber): RedirectResponse
    {
        $subscriber->update(['status' => 'unsubscribed', 'unsubscribed_at' => now()]);

        return back();
    }
}
