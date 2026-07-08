<?php

namespace App\Http\Controllers\Cms;

use App\Core\Media\MediaManager;
use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobListing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Receives public job applications. The target listing must be open.
 * Optional résumé is stored privately via the media library.
 */
class JobApplicationController extends Controller
{
    public function store(Request $request, MediaManager $media): RedirectResponse
    {
        // Honeypot.
        if ($request->filled('website')) {
            return back();
        }

        $data = $request->validate([
            'job_listing_id' => 'required|string',
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'phone'          => 'nullable|string|max:50',
            'cover_letter'   => 'nullable|string|max:5000',
            'resume'         => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        // Must be an open listing, else 404.
        $job = JobListing::open()->findOrFail($data['job_listing_id']);

        $resumeId = null;
        if ($request->hasFile('resume')) {
            // Private disk: résumés contain PII and must not be publicly fetchable.
            // Served only via the gated admin download route.
            $resumeId = $media->store($request->file('resume'), null, 'local')->id;
        }

        JobApplication::create([
            'job_listing_id'  => $job->id,
            'name'            => $data['name'],
            'email'           => $data['email'],
            'phone'           => $data['phone'] ?? null,
            'cover_letter'    => $data['cover_letter'] ?? null,
            'resume_media_id' => $resumeId,
            'status'          => 'new',
        ]);

        return back()->with('application_sent', true);
    }
}
