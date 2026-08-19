<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Form;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Public rendering + submission capture for custom forms (e.g. admission
 * enquiry forms). Validates against the form's own field schema.
 */
class FormController extends Controller
{
    public function show(string $slug): View
    {
        $form = Form::published()->where('slug', $slug)->firstOrFail();

        return view('site.form', compact('form'));
    }

    public function submit(Request $request, string $slug): RedirectResponse
    {
        $form = Form::published()->where('slug', $slug)->firstOrFail();

        // Honeypot.
        if ($request->filled('website')) {
            return back();
        }

        $rules = [];
        $labels = [];
        foreach ($form->fields ?? [] as $field) {
            $key = $field['key'] ?? $field['name'] ?? null;
            if (!$key) continue;
            
            $fieldType = $field['type'] ?? 'text';
            $rule = ($field['required'] ?? false) ? ['required'] : ['nullable'];
            $rule[] = $fieldType === 'email' ? 'email' : 'string';
            $rule[] = 'max:5000';
            $rules[$key] = implode('|', $rule);
            $labels[$key] = $field['label'] ?? ucfirst(str_replace('_', ' ', $key));
        }

        $validated = $request->validate($rules, [], $labels);

        $form->submissions()->create([
            'data' => $validated,
            'ip'   => $request->ip(),
        ]);

        AdminNotification::record('form', 'New submission: ' . $form->title, [
            'body' => Str::limit(collect($validated)->filter()->implode(' · '), 90),
            'url'  => url('/admin/forms/' . $form->id . '/submissions'),
            'icon' => 'inbox',
        ]);

        return back()->with('form_success', $form->success_message ?: 'Thank you! Your submission has been received.');
    }
}
