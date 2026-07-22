<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditTrailController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::query()
            ->when($request->user_id, fn ($q) => $q->where('causer_id', $request->user_id))
            ->when($request->log_name, fn ($q) => $q->where('log_name', $request->log_name))
            ->when($request->from, fn ($q) => $q->where('created_at', '>=', $request->from))
            ->when($request->to, fn ($q) => $q->where('created_at', '<=', $request->to . ' 23:59:59'))
            ->when($request->search, fn ($q) => $q->where('description', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $admins = User::all();
        $modules = ActivityLog::select('log_name')->distinct()->pluck('log_name')->toArray();

        return view('admin.audit.index', compact('logs', 'admins', 'modules'));
    }

    public function show(string $id)
    {
        $log = ActivityLog::findOrFail($id);
        return view('admin.audit.show', compact('log'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $logs = ActivityLog::query()
            ->when($request->user_id, fn ($q) => $q->where('causer_id', $request->user_id))
            ->when($request->log_name, fn ($q) => $q->where('log_name', $request->log_name))
            ->when($request->from, fn ($q) => $q->where('created_at', '>=', $request->from))
            ->when($request->to, fn ($q) => $q->where('created_at', '<=', $request->to . ' 23:59:59'))
            ->when($request->search, fn ($q) => $q->where('description', 'like', "%{$request->search}%"))
            ->latest()
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="audit_trail_export_' . now()->toDateString() . '.csv"',
        ];

        return new StreamedResponse(function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Module', 'Description', 'Causer ID', 'Subject ID', 'Subject Type', 'Properties', 'Created At']);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->id,
                    $log->log_name,
                    $log->description,
                    $log->causer_id,
                    $log->subject_id,
                    $log->subject_type,
                    json_encode($log->properties),
                    $log->created_at,
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }
}
