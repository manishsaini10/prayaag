<?php

namespace App\Http\Controllers\Admin;

use App\Core\Mail\MailManager;
use App\Http\Controllers\Controller;
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
        $queuedCount = EmailLog::where('status', 'queued')->count();

        return view('admin.email-logs.index', compact('logs', 'queuedCount'));
    }

    public function resend(string $id, MailManager $mailManager)
    {
        $log = EmailLog::findOrFail($id);

        $sentLog = $mailManager->send(
            templateKey: $log->template_key,
            data: [],
            to: $log->to_address,
            async: false
        );

        if ($sentLog && $sentLog->status === 'sent') {
            return back()->with('success', "✅ Email resent successfully to {$log->to_address} via " . strtoupper($sentLog->provider_used) . "!");
        } elseif ($sentLog && $sentLog->status === 'failed') {
            return back()->with('error', "❌ Email resend failed: {$sentLog->error_message}");
        }

        return back()->with('success', "Email resend processed for {$log->to_address}.");
    }

    public function flushQueue(MailManager $mailManager)
    {
        $queuedLogs = EmailLog::where('status', 'queued')->get();
        $count = 0;

        foreach ($queuedLogs as $log) {
            $mailManager->send(
                templateKey: $log->template_key,
                data: [],
                to: $log->to_address,
                async: false
            );
            $log->delete();
            $count++;
        }

        return back()->with('success', "✅ Flushed and sent {$count} queued emails in real-time!");
    }
}
