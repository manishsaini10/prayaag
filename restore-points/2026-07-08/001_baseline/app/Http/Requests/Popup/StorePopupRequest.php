<?php

namespace App\Http\Requests\Popup;

use Illuminate\Foundation\Http\FormRequest;

class StorePopupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:2', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:popups,slug'],
            'type' => ['required', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'in:draft,active,paused,scheduled,expired,archived'],
            'category_id' => ['nullable', 'string', 'exists:popup_categories,id'],
            'template_id' => ['nullable', 'string', 'exists:popup_templates,id'],
            'structure' => ['nullable', 'array'],
            'settings' => ['nullable', 'array'],
            'design' => ['nullable', 'array'],
            'styles' => ['nullable', 'array'],
            'custom_css' => ['nullable', 'string'],
            'custom_js' => ['nullable', 'string'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'frequency_type' => ['nullable', 'string'],
            'priority' => ['nullable', 'integer', 'min:0', 'max:999'],
            'meta' => ['nullable', 'array'],
            'noindex' => ['nullable', 'boolean'],
        ];
    }
}
