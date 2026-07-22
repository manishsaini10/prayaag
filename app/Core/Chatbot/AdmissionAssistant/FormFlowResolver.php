<?php

namespace App\Core\Chatbot\AdmissionAssistant;

use App\Core\Chatbot\MultiLLMRouter;
use App\Models\Form;
use App\Models\ChatbotConversationalForm;

class FormFlowResolver
{
    protected MultiLLMRouter $router;

    public function __construct(MultiLLMRouter $router)
    {
        $this->router = $router;
    }

    /**
     * Determine the next field that still needs a value.
     * Returns the field definition array or null when all required fields are filled.
     */
    public function nextField(ChatbotConversationalForm $convForm): ?array
    {
        $form = Form::findOrFail($convForm->form_id);
        $collected = json_decode($convForm->collected_data ?? '[]', true);
        foreach ($form->fields as $field) {
            if ($field['required'] && !array_key_exists($field['key'], $collected)) {
                return $field;
            }
        }
        return null;
    }

    /**
     * Generate a friendly Hinglish question for the given field.
     */
    public function askNextField(array $field, array $collectedSoFar): string
    {
        $prompt = "Ek friendly school admission assistant ki tarah, parent se '{$field['label']}' poocho. " .
            "Context: ab tak collect hua hai: " . json_encode($collectedSoFar) . ". " .
            "Ek hi chota, natural sawaal Hindi‑English mix (Hinglish) me puchho, jaisa parent aam taur pe bolta hai.";
        return $this->router->completion($prompt, model: 'fast');
    }
}
