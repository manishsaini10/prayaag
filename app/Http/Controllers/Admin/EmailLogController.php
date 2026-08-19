<?php

namespace App\Http\Controllers\Admin;

use App\Core\Mail\MailManager;
use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = EmailLog::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('search')) {
            $query->where('template_key', 'like', '%' . $request->search . '%')
                ->orWhere('subject', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('admin.email-logs.index', compact('logs'));
    }

    public function resend(string $id, MailManager $mailManager)
    {
        $log = EmailLog::findOrFail($id);

        $mailManager->send(
            templateKey: $log->template_key,
            data: [],
            to: $log->to_address
        );

        return back()->with('success', "Re-queued email for resend to {$log->to_address}.");
    }
}
