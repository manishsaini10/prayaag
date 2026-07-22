<?php

namespace App\Core\Chatbot\AdmissionAssistant;

use App\Core\Chatbot\MultiLLMRouter;

class FieldExtractor
{
    protected MultiLLMRouter $router;

    public function __construct(MultiLLMRouter $router)
    {
        $this->router = $router;
    }

    /**
     * Extract a single field value from a user's free‑text message.
     * Returns null when extraction fails or the user did not provide the value.
     */
    public function extract(string $userMessage, array $fieldDefinition): ?string
    {
        $prompt = <<<PROMPT
You are extracting a single form field value from a parent's message on a school admission chatbot.

Field to extract: "{$fieldDefinition['label']}" (type: {$fieldDefinition['type']})
User's message: "{$userMessage}"

Rules:
- Return ONLY the extracted value, nothing else.
- If the message does not contain a valid value for this field, return exactly: NULL
- Do not add explanations, labels, punctuation, or surrounding whitespace.
PROMPT;
        $response = $this->router->completion($prompt, model: 'fast');
        $value = trim($response);
        return $value === 'NULL' ? null : $value;
    }
}
