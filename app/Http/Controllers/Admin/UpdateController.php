<?php

namespace App\Http\Controllers\Admin;

use App\Core\Updater\AutoDeployerService;
use App\Core\Updater\UpdateManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UpdateController extends Controller
{
    public function __construct(
        protected UpdateManager $updater,
        protected AutoDeployerService $deployer
    ) {}

    /* ─────────────────────────────────────────────────────────────
     |  GET /admin/updates  — Main update dashboard
     * ─────────────────────────────────────────────────────────── */
    public function index()
    {
        return view('admin.updates.index', [
            'currentVersion' => $this->updater->currentVersion(),
            'history'        => $this->updater->history(15),
            'backups'        => $this->updater->listBackups(),
            'systemInfo'     => $this->deployer->getSystemInfo(),
            'webhookUrl'     => url('/api/deploy/webhook?token=' . substr(hash('sha256', config('app.key') . 'deploy_secret'), 0, 32)),
        ]);
    }

    /* ─────────────────────────────────────────────────────────────
     |  POST /admin/updates/git-pull  — 1-Click Git Auto-Sync (with Auto-Backup)
     * ─────────────────────────────────────────────────────────── */
    public function gitPull(Request $request)
    {
        $branch    = $request->input('branch', 'main');
        $appliedBy = auth()->user()?->name ?? 'Admin';
        $result    = $this->deployer->backupAndDeploy($branch, $appliedBy);

        if ($result['success']) {
            $msg = "✅ Update Applied Successfully! " . $result['message'];
            if (!empty($result['backup_path'])) {
                $msg .= " (Pre-update backup saved: " . basename($result['backup_path']) . ")";
            }
            return back()->with('success', $msg)
                         ->with('deploy_logs', $result['logs']);
        }

        return back()->with('error', "❌ Git Auto-Sync Failed: " . $result['message'])
                     ->with('deploy_logs', $result['logs']);
    }


    /* ─────────────────────────────────────────────────────────────
     |  POST /admin/updates/upload  — Upload & validate package
     * ─────────────────────────────────────────────────────────── */
    public function upload(Request $request)
    {
        $request->validate([
            'package' => ['required', 'file', 'mimes:zip', 'max:51200'], // 50 MB max
        ]);

        $file    = $request->file('package');
        $tmpPath = storage_path('app/update-temp/' . $file->getClientOriginalName());

        \Illuminate\Support\Facades\File::ensureDirectoryExists(storage_path('app/update-temp'));
        $file->move(storage_path('app/update-temp'), $file->getClientOriginalName());

        $result = $this->updater->validatePackage($tmpPath);

        if (!$result['valid']) {
            \Illuminate\Support\Facades\File::delete($tmpPath);
            return back()->with('error', $result['error']);
        }

        // Store path in session for confirmation step
        session(['pending_update_path' => $tmpPath, 'pending_update_manifest' => $result['manifest']]);

        return redirect()->route('admin.updates.confirm');
    }

    /* ─────────────────────────────────────────────────────────────
     |  GET /admin/updates/confirm  — Review & confirm
     * ─────────────────────────────────────────────────────────── */
    public function confirm()
    {
        if (!session('pending_update_path') || !session('pending_update_manifest')) {
            return redirect()->route('admin.updates.index')->with('error', 'No pending update found. Please upload a package first.');
        }

        return view('admin.updates.confirm', [
            'currentVersion' => $this->updater->currentVersion(),
            'manifest'       => session('pending_update_manifest'),
        ]);
    }

    /* ─────────────────────────────────────────────────────────────
     |  POST /admin/updates/apply  — Apply the update
     * ─────────────────────────────────────────────────────────── */
    public function apply(Request $request)
    {
        $zipPath  = session('pending_update_path');
        $manifest = session('pending_update_manifest');

        if (!$zipPath || !$manifest) {
            return redirect()->route('admin.updates.index')->with('error', 'Session expired. Please upload the package again.');
        }

        // Clear session immediately so double-submit is prevented
        session()->forget(['pending_update_path', 'pending_update_manifest']);

        $appliedBy = auth()->user()?->name ?? 'Admin';

        // Step 1 — Backup
        try {
            $backupPath = $this->updater->createBackup($manifest['version']);
        } catch (\Throwable $e) {
            Log::error('Backup failed before update', ['error' => $e->getMessage()]);
            return redirect()->route('admin.updates.index')
                ->with('error', 'Backup failed: ' . $e->getMessage() . '. Update aborted for safety.');
        }

        // Step 2 — Apply
        $result = $this->updater->applyUpdate($zipPath, $manifest, $backupPath, $appliedBy);

        if ($result['success']) {
            return redirect()->route('admin.updates.index')
                ->with('success', "✅ CMS updated to v{$result['version']} successfully! A backup was saved automatically.");
        }

        return redirect()->route('admin.updates.index')
            ->with('error', 'Update failed: ' . $result['error'] . ' — You can rollback from the history table below.');
    }

    /* ─────────────────────────────────────────────────────────────
     |  POST /admin/updates/{id}/rollback  — Rollback a specific update
     * ─────────────────────────────────────────────────────────── */
    public function rollback(int $id)
    {
        $result = $this->updater->rollback($id);

        if ($result['success']) {
            return back()->with('success', 'Rollback completed successfully.');
        }

        return back()->with('error', 'Rollback failed: ' . $result['error']);
    }

    /* ─────────────────────────────────────────────────────────────
     |  POST /admin/updates/backup  — Manual backup
     * ─────────────────────────────────────────────────────────── */
    public function backup()
    {
        try {
            $path = $this->updater->createBackup('manual');
            return back()->with('success', 'Backup created successfully: ' . basename($path));
        } catch (\Throwable $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }
}
