<?php

namespace App\Core\Privacy\Services;

use App\Models\Enquiry;
use App\Models\Popup\PopupLead;
use App\Models\Chatbot\ChatbotLead;
use App\Models\Subscriber;
use App\Models\FormSubmission;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Storage;

class DataExportService
{
    public function collect(string $email): array
    {
        return [
            'enquiries'        => Enquiry::where('email', $email)->get()->toArray(),
            'popup_leads'      => PopupLead::where('email', $email)->get()->toArray(),
            'chatbot_leads'    => ChatbotLead::where('email', $email)->get()->toArray(),
            'subscribers'      => Subscriber::where('email', $email)->get()->toArray(),
            'form_submissions' => FormSubmission::where('email', $email)->get()->toArray(),
            'job_applications' => JobApplication::where('email', $email)->get()->toArray(),
        ];
    }

    public function generateExportFile(string $email): string
    {
        $data = $this->collect($email);
        $filename = 'privacy-exports/' . md5($email . now()) . '.json';

        Storage::disk('local')->put($filename, json_encode($data, JSON_PRETTY_PRINT));

        return $filename;
    }
}
