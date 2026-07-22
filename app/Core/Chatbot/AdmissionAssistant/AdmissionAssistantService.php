<?php

namespace App\Core\Chatbot\AdmissionAssistant;

use App\Models\Chatbot\ChatbotLead;
use Illuminate\Support\Facades\Log;

/**
 * Admission Assistant — conversational form filling for admission enquiries via chatbot.
 *
 * Flow:
 *   1. Detect intent = 'admission'
 *   2. Conversationally collect: parent_name → child_name → class → dob → phone
 *   3. Save as ChatbotLead
 *   4. Confirm + thank user
 */
class AdmissionAssistantService
{
    /**
     * Fields to collect in order, with natural language questions.
     */
    protected array $fields = [
        'parent_name' => [
            'question' => "Wonderful! I'll help you with the **admission enquiry**. 🏫\n\nMay I start with your **name** (parent/guardian)?",
            'label'    => 'Parent Name',
        ],
        'child_name' => [
            'question' => "Thank you, {parent_name}! What is your **child's name**?",
            'label'    => "Child's Name",
        ],
        'class_seeking' => [
            'question' => "Which **class** are you seeking admission for? (e.g., Nursery, Class 1, Class 6)",
            'label'    => 'Class',
        ],
        'child_dob' => [
            'question' => "What is {child_name}'s **date of birth**? (e.g., 10 March 2019)",
            'label'    => "Child's DOB",
        ],
        'phone' => [
            'question' => "Please share your **mobile number** so our admissions team can contact you.",
            'label'    => 'Phone',
        ],
        'email' => [
            'question' => "Your **email address**? (Type 'skip' if you prefer not to share)",
            'label'    => 'Email',
        ],
    ];

    /**
     * Process a user message within an admission assistant session.
     * Returns the next bot message to send.
     *
     * @param  string  $conversationId
     * @param  string  $userMessage
     * @param  array   &$sessionData   Current session (stored in conversation meta)
     */
    public function handle(string $conversationId, string $userMessage, array &$sessionData): string
    {
        if (!isset($sessionData['admission_assistant'])) {
            $sessionData['admission_assistant'] = [
                'active'    => true,
                'step'      => 0,
                'collected' => [],
            ];
        }

        $state     = &$sessionData['admission_assistant'];
        $fieldKeys = array_keys($this->fields);
        $step      = $state['step'];

        // Store answer for previous field
        if ($step > 0) {
            $prevKey = $fieldKeys[$step - 1];
            $answer  = trim($userMessage);
            if (strtolower($answer) !== 'skip') {
                $state['collected'][$prevKey] = $answer;
            }
        }

        // All fields filled → save lead
        if ($step >= count($fieldKeys)) {
            $this->saveLead($state['collected'], $conversationId);
            $state['active'] = false;
            return $this->completionMessage($state['collected']);
        }

        // Ask next question
        $nextKey   = $fieldKeys[$step];
        $question  = $this->fields[$nextKey]['question'];

        // Replace named placeholders
        foreach ($state['collected'] as $k => $v) {
            $question = str_replace("{{$k}}", $v, $question);
        }

        $state['step']++;

        return $question;
    }

    /**
     * First greeting when admission intent detected for the first time.
     */
    public function greeting(): string
    {
        return "I'd love to help with **admissions at Prayaag School**! 🌟 I'll collect a few details and our admissions team will be in touch with you shortly.\n\nLet's begin!";
    }

    /**
     * Save collected data as a ChatbotLead.
     */
    protected function saveLead(array $data, string $conversationId): void
    {
        try {
            ChatbotLead::create([
                'name'           => $data['parent_name']   ?? 'Unknown',
                'email'          => $data['email']         ?? null,
                'phone'          => $data['phone']         ?? null,
                'interest'       => 'Admission - ' . ($data['class_seeking'] ?? 'Unknown Class'),
                'status'         => 'new',
                'form_data'      => array_merge($data, [
                    'source'          => 'chatbot_admission_assistant',
                    'conversation_id' => $conversationId,
                ]),
            ]);

            Log::info('Admission lead saved via chatbot', ['conversation_id' => $conversationId, 'data' => $data]);
        } catch (\Throwable $e) {
            Log::error('Failed to save chatbot admission lead: ' . $e->getMessage());
        }
    }

    protected function completionMessage(array $collected): string
    {
        $parent = $collected['parent_name'] ?? 'there';
        $child  = $collected['child_name']  ?? 'your child';
        $class  = $collected['class_seeking'] ?? 'the requested class';

        return "Thank you, **{$parent}**! 🎉 We've received your admission enquiry for **{$child}** (seeking admission to **{$class}**).\n\nOur admissions team will call you within **1–2 working days**. You can also visit our school between **8 AM – 2 PM** on weekdays.\n\nIs there anything else I can help you with?";
    }

    /**
     * Check if admission assistant session is active.
     */
    public function isActive(array $sessionData): bool
    {
        return ($sessionData['admission_assistant']['active'] ?? false) === true;
    }
}
