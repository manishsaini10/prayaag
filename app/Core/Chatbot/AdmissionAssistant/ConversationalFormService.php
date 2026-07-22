<?php

namespace App\Core\Chatbot\AdmissionAssistant;

use App\Core\Chatbot\MultiLLMRouter;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Form; // assuming existing Form model
use App\Models\ChatbotConversationalForm;

class ConversationalFormService
{
    public function __construct(protected MultiLLMRouter $router) {}

    /**
     * Start or retrieve an in‑progress conversational form for given conversation.
     */
    public function getOrCreate(string $conversationId, string $formId): ChatbotConversationalForm
    {
        $form = ChatbotConversationalForm::firstOrCreate(
            ['conversation_id' => $conversationId, 'form_id' => $formId],
            [
                'id' => (string) Str::ulid(),
                'collected_data' => json_encode([]),
                'status' => 'in_progress',
            ]
        );
        return $form;
    }

    /**
     * Update collected data for a field.
     */
    public function storeField(ChatbotConversationalForm $convForm, string $fieldKey, ?string $value): void
    {
        $data = json_decode($convForm->collected_data ?? '[]', true);
        if ($value !== null) {
            $data[$fieldKey] = $value;
        }
        $convForm->collected_data = json_encode($data);
        $convForm->current_field_key = null;
        $convForm->save();
    }

    /**
     * Check if all required fields are filled.
     */
    public function isCompleted(ChatbotConversationalForm $convForm): bool
    {
        $form = Form::findOrFail($convForm->form_id);
        $required = collect($form->fields)->where('required', true)->pluck('key')->all();
        $filled = array_keys(json_decode($convForm->collected_data ?? '[]', true));
        return empty(array_diff($required, $filled));
    }
}
