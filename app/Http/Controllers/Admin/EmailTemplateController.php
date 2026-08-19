<?php

namespace App\Http\Controllers\Admin;

use App\Core\Mail\MailManager;
use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\EmailTemplateRevision;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailTemplateController extends Controller
{
    public function index(): View
    {
        $templatesGrouped = EmailTemplate::all()->groupBy('module');
        return view('admin.email-templates.index', compact('templatesGrouped'));
    }

    public function edit(string $id): View
    {
        $template = EmailTemplate::with('revisions')->findOrFail($id);
        return view('admin.email-templates.edit', compact('template'));
    }

    public function update(Request $request, string $id)
    {
        $template = EmailTemplate::findOrFail($id);

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        // Save revision snapshot before updating
        EmailTemplateRevision::create([
            'email_template_id' => $template->id,
            'subject' => $template->subject,
            'body_html' => $template->body_html,
            'created_by' => auth()->id(),
        ]);

        $template->update([
            'subject' => $validated['subject'],
            'body_html' => $validated['body_html'],
            'is_active' => $request->has('is_active') ? (bool) $validated['is_active'] : $template->is_active,
        ]);

        return redirect()->route('admin.email-templates.index')
            ->with('success', "Template '{$template->template_key}' updated.");
    }

    public function testSend(Request $request, string $id, MailManager $mailManager)
    {
        $template = EmailTemplate::findOrFail($id);
        $adminEmail = auth()->user()->email;

        // Populate dummy placeholder data
        $dummyData = [];
        foreach ($template->available_placeholders ?? [] as $key) {
            $dummyData[$key] = "[Sample {$key}]";
        }

        $log = $mailManager->send(
            templateKey: $template->template_key,
            data: $dummyData,
            to: $adminEmail
        );

        return back()->with('success', "Test email for '{$template->template_key}' sent to {$adminEmail}.");
    }

    public function revert(string $id, string $revisionId)
    {
        $template = EmailTemplate::findOrFail($id);
        $revision = EmailTemplateRevision::where('email_template_id', $template->id)->findOrFail($revisionId);

        $template->update([
            'subject' => $revision->subject,
            'body_html' => $revision->body_html,
        ]);

        return back()->with('success', 'Template reverted to selected revision.');
    }

    public function toggle(string $id)
    {
        $template = EmailTemplate::findOrFail($id);
        $template->update(['is_active' => !$template->is_active]);

        return back()->with('success', "Template status toggled.");
    }
}
