<?php

namespace App\Core\Privacy\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Core\Privacy\Models\DataPrivacyRequest;
use App\Core\Privacy\Services\DataDeletionService;
use App\Core\Privacy\Services\DataExportService;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrivacyDashboardController extends Controller
{
    public function index(Request $request)
    {
        $requests = DataPrivacyRequest::query()
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->type, fn ($q) => $q->where('request_type', $request->type))
            ->latest()
            ->paginate(15);

        return view('admin.privacy.index', compact('requests'));
    }

    public function approve(Request $request, string $id, DataExportService $exporter, DataDeletionService $deleter)
    {
        $privacyRequest = DataPrivacyRequest::findOrFail($id);

        if (!in_array($privacyRequest->status, ['verified', 'pending'])) {
            return back()->with('error', 'Request has already been processed.');
        }

        $admin = auth()->user();

        if ($privacyRequest->request_type === 'export') {
            $filePath = $exporter->generateExportFile($privacyRequest->email);
            $privacyRequest->update([
                'status'           => 'completed',
                'processed_by'     => $admin?->name ?? 'System',
                'processed_at'     => now(),
                'export_file_path' => $filePath,
                'notes'            => $request->notes,
            ]);

            // Log security audit
            ActivityLog::create([
                'log_name'     => 'privacy',
                'description'  => "Approved PII export request for {$privacyRequest->email}",
                'subject_type' => DataPrivacyRequest::class,
                'subject_id'   => $privacyRequest->id,
                'causer_type'  => $admin ? get_class($admin) : null,
                'causer_id'    => $admin?->id,
                'properties'   => ['email' => $privacyRequest->email],
            ]);
        } elseif ($privacyRequest->request_type === 'delete') {
            $deleter->anonymize($privacyRequest->email);
            $privacyRequest->update([
                'status'       => 'completed',
                'processed_by' => $admin?->name ?? 'System',
                'processed_at' => now(),
                'notes'        => $request->notes,
            ]);

            // Log security audit
            ActivityLog::create([
                'log_name'     => 'privacy',
                'description'  => "Approved PII deletion/anonymization request for {$privacyRequest->email}",
                'subject_type' => DataPrivacyRequest::class,
                'subject_id'   => $privacyRequest->id,
                'causer_type'  => $admin ? get_class($admin) : null,
                'causer_id'    => $admin?->id,
                'properties'   => ['email' => $privacyRequest->email],
            ]);
        }

        return back()->with('success', 'Privacy request approved and processed successfully.');
    }

    public function reject(Request $request, string $id)
    {
        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $privacyRequest = DataPrivacyRequest::findOrFail($id);
        $admin = auth()->user();

        $privacyRequest->update([
            'status'       => 'rejected',
            'processed_by' => $admin?->name ?? 'System',
            'processed_at' => now(),
            'notes'        => $request->notes,
        ]);

        // Log security audit
        ActivityLog::create([
            'log_name'     => 'privacy',
            'description'  => "Rejected PII request for {$privacyRequest->email}",
            'subject_type' => DataPrivacyRequest::class,
            'subject_id'   => $privacyRequest->id,
            'causer_type'  => $admin ? get_class($admin) : null,
            'causer_id'    => $admin?->id,
            'properties'   => ['email' => $privacyRequest->email, 'reason' => $request->notes],
        ]);

        return back()->with('success', 'Privacy request rejected.');
    }

    public function download(string $id)
    {
        $privacyRequest = DataPrivacyRequest::findOrFail($id);

        if ($privacyRequest->request_type !== 'export' || !$privacyRequest->export_file_path) {
            abort(404);
        }

        if (!Storage::disk('local')->exists($privacyRequest->export_file_path)) {
            abort(404, 'File has expired or been deleted.');
        }

        // Log access audit
        ActivityLog::create([
            'log_name'     => 'privacy',
            'description'  => "Downloaded PII export file for {$privacyRequest->email}",
            'subject_type' => DataPrivacyRequest::class,
            'subject_id'   => $privacyRequest->id,
            'causer_type'  => auth()->user() ? get_class(auth()->user()) : null,
            'causer_id'    => auth()->id(),
        ]);

        return Storage::disk('local')->download($privacyRequest->export_file_path, "pii-export-{$privacyRequest->email}.json");
    }
}
