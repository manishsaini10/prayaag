<?php

namespace App\Core\Privacy\Services;

use App\Models\Enquiry;
use App\Models\Popup\PopupLead;
use App\Models\Chatbot\ChatbotLead;
use App\Models\Subscriber;
use App\Models\FormSubmission;
use App\Models\JobApplication;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;

class DataDeletionService
{
    public function anonymize(string $email): void
    {
        $anonymizedEmail = 'deleted-' . substr(md5($email), 0, 10) . '@anonymized.local';

        // 1. Anonymize Enquiries
        Enquiry::where('email', $email)->update([
            'name'    => 'Deleted User',
            'email'   => $anonymizedEmail,
            'phone'   => null,
            'subject' => null,
            'message' => '[deleted per privacy request]',
        ]);

        // 2. Anonymize Popup Leads
        PopupLead::where('email', $email)->update([
            'name'      => 'Deleted User',
            'email'     => $anonymizedEmail,
            'phone'     => null,
            'form_data' => null,
            'notes'     => null,
        ]);

        // 3. Anonymize Chatbot Leads
        ChatbotLead::where('email', $email)->update([
            'name'  => 'Deleted User',
            'email' => $anonymizedEmail,
            'phone' => null,
            'notes' => null,
        ]);

        // 4. Hard-delete Subscribers (no FK constraints)
        Subscriber::where('email', $email)->delete();

        // 5. Anonymize custom Form Submissions
        FormSubmission::where('email', $email)->update([
            'email' => $anonymizedEmail,
            'data'  => null, // Clear custom payload fields containing PII
        ]);

        // 6. Delete Resumes and anonymize Job Applications
        $jobApps = JobApplication::where('email', $email)->get();
        foreach ($jobApps as $app) {
            if ($app->resume_media_id) {
                $media = Media::find($app->resume_media_id);
                if ($media) {
                    rescue(fn () => Storage::disk($media->disk ?? 'public')->delete($media->path), null, false);
                    $media->delete();
                }
            }
            $app->update([
                'name'            => 'Deleted User',
                'email'           => $anonymizedEmail,
                'phone'           => null,
                'cover_letter'    => '[deleted per privacy request]',
                'resume_media_id' => null,
                'meta'            => null,
            ]);
        }
    }
}
