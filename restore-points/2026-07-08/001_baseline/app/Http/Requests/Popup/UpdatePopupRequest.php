<?php

namespace App\Http\Requests\Popup;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePopupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'min:2', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', 'unique:popups,slug,' . $this->route('popup')?->id],
            'type' => ['sometimes', 'string', 'max:50'],
            'status' => ['sometimes', 'string', 'in:draft,active,paused,scheduled,expired,archived'],
            'category_id' => ['nullable', 'string', 'exists:popup_categories,id'],
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
        ];
    }
}
