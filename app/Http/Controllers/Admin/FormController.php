<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Admission Forms builder. Forms are defined by a JSON field schema edited in
 * a dynamic builder; the public side renders and captures submissions.
 */
class FormController extends Controller
{
    public function index(): View
    {
        $forms = Form::withCount('submissions')->latest()->get();

        return view('admin.forms.index', compact('forms'));
    }

    public function create(): View
    {
        return view('admin.forms.builder', ['form' => new Form(['fields' => []]), 'mode' => 'create']);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title']);
        $form = Form::create($data);

        return redirect()->route('admin.forms.edit', $form)->with('status', 'Form created.');
    }

    public function edit(Form $form): View
    {
        return view('admin.forms.builder', ['form' => $form, 'mode' => 'edit']);
    }

    public function update(Request $request, Form $form): RedirectResponse
    {
        $form->update($this->validated($request));

        return back()->with('status', 'Form saved.');
    }

    public function destroy(Form $form): RedirectResponse
    {
        $form->delete();

        return redirect()->route('admin.forms.index')->with('status', 'Form deleted.');
    }

    public function submissions(Form $form): View
    {
        $form->load(['submissions' => fn ($q) => $q->latest()]);

        return view('admin.forms.submissions', compact('form'));
    }

    /* ----------------------------------------------------------------- */

    /** @return array<string, mixed> */
    protected function validated(Request $request): array
    {
        $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'success_message' => 'nullable|string|max:500',
            'is_published'    => 'nullable',
        ]);

        $allowed = ['text', 'email', 'tel', 'number', 'textarea', 'select', 'date'];
        $fields = [];
        $usedKeys = [];

        foreach ($request->input('fields', []) as $f) {
            $label = trim($f['label'] ?? '');
            if ($label === '') {
                continue;
            }

            $key = Str::slug($label, '_') ?: 'field';
            while (in_array($key, $usedKeys, true)) {
                $key .= '_' . (count($usedKeys) + 1);
            }
            $usedKeys[] = $key;

            $fields[] = [
                'key'         => $key,
                'label'       => $label,
                'type'        => in_array($f['type'] ?? 'text', $allowed, true) ? $f['type'] : 'text',
                'required'    => ! empty($f['required']),
                'placeholder' => trim($f['placeholder'] ?? ''),
                'options'     => array_values(array_filter(array_map('trim', explode(',', $f['options'] ?? '')))),
            ];
        }

        return [
            'title'           => $request->input('title'),
            'description'     => $request->input('description'),
            'success_message' => $request->input('success_message') ?: 'Thank you! Your submission has been received.',
            'is_published'    => $request->boolean('is_published'),
            'fields'          => $fields,
        ];
    }

    protected function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'form';
        $slug = $base;
        while (Form::where('slug', $slug)->exists()) {
            $slug = $base . '-' . Str::lower(Str::random(4));
        }

        return $slug;
    }
}
