<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\ChatbotFormField;
use App\Models\Chatbot\ChatbotLead;
use Illuminate\Http\Request;

class AdminPreChatFormController extends Controller
{
    public function fields()
    {
        $fields = ChatbotFormField::ordered()->get();
        $submissionsCount = ChatbotLead::whereNotNull('form_data')->count();
        return view('admin.chatbot.form-fields', compact('fields', 'submissionsCount'));
    }

    public function storeField(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'field_key' => 'required|string|max:100|unique:chatbot_form_fields,field_key',
            'field_type' => 'required|string|in:text,email,tel,select,textarea,number',
            'placeholder' => 'nullable|string|max:255',
            'options' => 'nullable|array',
            'options.*' => 'string|max:255',
            'is_required' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['is_required'] = $request->has('is_required');
        $validated['is_active'] = true;
        if (!isset($validated['sort_order'])) {
            $validated['sort_order'] = ChatbotFormField::max('sort_order') + 1;
        }

        ChatbotFormField::create($validated);

        return redirect()->back()->with('success', 'Form field created successfully.');
    }

    public function updateField(Request $request, $id)
    {
        $field = ChatbotFormField::findOrFail($id);

        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'field_key' => 'required|string|max:100|unique:chatbot_form_fields,field_key,' . $id,
            'field_type' => 'required|string|in:text,email,tel,select,textarea,number',
            'placeholder' => 'nullable|string|max:255',
            'options' => 'nullable|array',
            'options.*' => 'string|max:255',
            'is_required' => 'boolean',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_required'] = $request->has('is_required');
        $validated['is_active'] = $request->has('is_active');

        $field->update($validated);

        return redirect()->back()->with('success', 'Form field updated successfully.');
    }

    public function destroyField($id)
    {
        $field = ChatbotFormField::findOrFail($id);
        $field->delete();

        return redirect()->back()->with('success', 'Form field deleted successfully.');
    }

    public function toggleField($id)
    {
        $field = ChatbotFormField::findOrFail($id);
        $field->update(['is_active' => !$field->is_active]);

        return redirect()->back()->with('success', 'Field ' . ($field->is_active ? 'activated' : 'deactivated') . ' successfully.');
    }

    public function reorderFields(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'string|exists:chatbot_form_fields,id',
        ]);

        foreach ($request->order as $index => $id) {
            ChatbotFormField::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function submissions()
    {
        $fields = ChatbotFormField::ordered()->get();
        $leads = ChatbotLead::whereNotNull('form_data')
            ->with('visitor')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.chatbot.form-submissions', compact('fields', 'leads'));
    }
}
