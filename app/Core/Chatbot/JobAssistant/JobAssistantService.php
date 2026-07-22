<?php

namespace App\Core\Chatbot\JobAssistant;

use App\Models\JobApplication;
use App\Models\JobListing;
use Illuminate\Support\Facades\Log;

/**
 * Job Assistant — conversational form filling for job applicants via chatbot.
 *
 * Flow:
 *   1. Detect intent = 'job'
 *   2. Ask questions one by one: name → applying_for → experience → phone → email
 *   3. Save to job_applications table
 *   4. Confirm and hand off
 */
class JobAssistantService
{
    /**
     * Fields to collect in order, with natural language questions.
     */
    protected array $fields = [
        'applicant_name' => [
            'question' => "Great! I'd be happy to help you apply. 😊 May I have your **full name** first?",
            'label'    => 'Full Name',
        ],
        'applying_for' => [
            'question' => "Thank you, {applicant_name}! Which **position** are you applying for? (e.g., Teacher, Admin Staff, Lab Assistant)",
            'label'    => 'Position',
        ],
        'experience' => [
            'question' => "How many **years of experience** do you have in this field?",
            'label'    => 'Experience',
        ],
        'phone' => [
            'question' => "Please share your **contact number** so we can reach you.",
            'label'    => 'Phone',
        ],
        'email' => [
            'question' => "And your **email address**? (Type 'skip' if you prefer not to share)",
            'label'    => 'Email',
        ],
    ];

    /**
     * Process a user message within a job assistant session.
     * Returns the next bot message to send.
     *
     * @param  string  $conversationId  Chatbot conversation ID
     * @param  string  $userMessage     User's latest message
     * @param  array   &$sessionData    Current session data (passed by reference, stored in conversation meta)
     */
    public function handle(string $conversationId, string $userMessage, array &$sessionData): string
    {
        // Initialize session if not started
        if (!isset($sessionData['job_assistant'])) {
            $sessionData['job_assistant'] = [
                'active'    => true,
                'step'      => 0,
                'collected' => [],
            ];
        }

        $state     = &$sessionData['job_assistant'];
        $fieldKeys = array_keys($this->fields);
        $step      = $state['step'];

        // If all fields already collected → should be done
        if ($step >= count($fieldKeys)) {
            return $this->completionMessage($state['collected']);
        }

        // Store the answer for the CURRENT field (step > 0 means we already asked one)
        if ($step > 0) {
            $currentKey = $fieldKeys[$step - 1];
            $answer = trim($userMessage);
            if (strtolower($answer) !== 'skip') {
                $state['collected'][$currentKey] = $answer;
            }
        }

        // Check if all fields filled
        if ($step >= count($fieldKeys)) {
            // Save application
            $this->saveApplication($state['collected'], $conversationId);
            $state['active'] = false;
            return $this->completionMessage($state['collected']);
        }

        // Ask next question
        $nextKey      = $fieldKeys[$step];
        $nextField    = $this->fields[$nextKey];
        $question     = $nextField['question'];

        // Replace placeholders like {applicant_name}
        foreach ($state['collected'] as $key => $value) {
            $question = str_replace("{{$key}}", $value, $question);
        }

        $state['step']++;

        return $question;
    }

    /**
     * First greeting when job intent is detected.
     */
    public function greeting(): string
    {
        return "I can help you with job applications at **Prayaag School**! 🎓 I'll collect some basic details and our HR team will get in touch with you shortly.\n\nLet's begin!";
    }

    /**
     * Save collected data to job_applications table.
     */
    protected function saveApplication(array $data, string $conversationId): void
    {
        try {
            // Try to find a matching job listing
            $jobListingId = null;
            if (!empty($data['applying_for'])) {
                $listing = JobListing::where('title', 'like', '%' . $data['applying_for'] . '%')
                    ->where('is_active', true)
                    ->first();
                $jobListingId = $listing?->id;
            }

            JobApplication::create([
                'job_listing_id' => $jobListingId,
                'name'           => $data['applicant_name'] ?? 'Unknown',
                'email'          => $data['email']          ?? null,
                'phone'          => $data['phone']          ?? null,
                'cover_letter'   => 'Applied via chatbot. Position: ' . ($data['applying_for'] ?? 'N/A') . '. Experience: ' . ($data['experience'] ?? 'N/A') . ' years.',
                'status'         => 'pending',
                'meta'           => array_merge($data, ['source' => 'chatbot', 'conversation_id' => $conversationId]),
            ]);

            Log::info('Job application saved via chatbot', ['conversation_id' => $conversationId, 'data' => $data]);
        } catch (\Throwable $e) {
            Log::error('Failed to save chatbot job application: ' . $e->getMessage());
        }
    }

    protected function completionMessage(array $collected): string
    {
        $name = $collected['applicant_name'] ?? 'there';
        $position = $collected['applying_for'] ?? 'the position';
        return "Thank you, **{$name}**! 🎉 Your application for **{$position}** has been recorded. Our HR team will review your profile and contact you within 2–3 working days.\n\nIs there anything else I can help you with?";
    }

    /**
     * Check if a job assistant session is active for this conversation.
     */
    public function isActive(array $sessionData): bool
    {
        return ($sessionData['job_assistant']['active'] ?? false) === true;
    }
}
