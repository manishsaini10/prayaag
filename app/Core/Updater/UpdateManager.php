<?php

namespace App\Core\Updater;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ZipArchive;

/**
 * CMS Update Manager
 * Handles upload, verification, backup, and application of CMS update packages.
 *
 * Update Package Format (update-v1.3.0.zip):
 *   manifest.json     → version, description, requires, files[], has_migrations
 *   files/            → changed PHP/CSS/JS files mirroring project root structure
 *   migrations/       → new migration files to run (optional)
 */
class UpdateManager
{
    protected string $basePath;
    protected string $backupDir;
    protected string $tempDir;

    public function __construct()
    {
        $this->basePath  = base_path();
        $this->backupDir = storage_path('backups/updates');
        $this->tempDir   = storage_path('app/update-temp');
    }

    /* ─────────────────────────────────────────────────────────────
     |  Current Version
     * ─────────────────────────────────────────────────────────── */

    public function currentVersion(): string
    {
        return config('cms.version', '1.0.0');
    }

    /* ─────────────────────────────────────────────────────────────
     |  Validate Uploaded Package
     * ─────────────────────────────────────────────────────────── */

    public function validatePackage(string $zipPath): array
    {
        if (!file_exists($zipPath)) {
            return ['valid' => false, 'error' => 'Package file not found.'];
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            return ['valid' => false, 'error' => 'Cannot open ZIP file. File may be corrupted.'];
        }

        // Must have manifest.json at root
        $manifestJson = $zip->getFromName('manifest.json');
        if ($manifestJson === false) {
            $zip->close();
            return ['valid' => false, 'error' => 'Invalid package: manifest.json is missing.'];
        }

        $manifest = json_decode($manifestJson, true);
        if (!$manifest || empty($manifest['version'])) {
            $zip->close();
            return ['valid' => false, 'error' => 'Invalid manifest: version field is required.'];
        }

        // Check minimum version requirement
        if (!empty($manifest['requires']) && version_compare($this->currentVersion(), $manifest['requires'], '<')) {
            $zip->close();
            return [
                'valid' => false,
                'error' => "This update requires CMS v{$manifest['requires']} or higher. You are on v{$this->currentVersion()}.",
            ];
        }

        // Check not downgrading
        if (version_compare($manifest['version'], $this->currentVersion(), '<=')) {
            $zip->close();
            return [
                'valid'    => false,
                'error'    => "Package version {$manifest['version']} is not newer than current version {$this->currentVersion()}.",
            ];
        }

        $zip->close();

        return [
            'valid'    => true,
            'manifest' => $manifest,
            'version'  => $manifest['version'],
        ];
    }

    /* ─────────────────────────────────────────────────────────────
     |  Create Pre-Update Backup
     * ─────────────────────────────────────────────────────────── */

    public function createBackup(string $version): string
    {
        File::ensureDirectoryExists($this->backupDir);

        $timestamp  = now()->format('Ymd_His');
        $backupName = "pre-update-{$version}-{$timestamp}.zip";
        $backupPath = $this->backupDir . '/' . $backupName;

        $zip = new ZipArchive();
        $zip->open($backupPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Backup critical directories
        $dirsToBackup = ['app', 'config', 'routes', 'resources/views', 'public/site.css', 'public/admin.css'];

        foreach ($dirsToBackup as $item) {
            $fullPath = $this->basePath . '/' . $item;
            if (is_file($fullPath)) {
                $zip->addFile($fullPath, $item);
            } elseif (is_dir($fullPath)) {
                $this->addDirToZip($zip, $fullPath, $item);
            }
        }

        $zip->close();
        return $backupPath;
    }

    /* ─────────────────────────────────────────────────────────────
     |  Apply Update Package
     * ─────────────────────────────────────────────────────────── */

    public function applyUpdate(string $zipPath, array $manifest, string $backupPath, string $appliedBy): array
    {
        $newVersion = $manifest['version'];

        // Create DB record
        $updateId = DB::table('cms_updates')->insertGetId([
            'version'          => $newVersion,
            'previous_version' => $this->currentVersion(),
            'package_name'     => basename($zipPath),
            'changelog'        => $manifest['changelog'] ?? null,
            'status'           => 'applying',
            'applied_by'       => $appliedBy,
            'backup_path'      => $backupPath,
            'manifest'         => json_encode($manifest),
            'applied_at'       => now(),
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        try {
            // Extract to temp
            File::ensureDirectoryExists($this->tempDir);
            $extractDir = $this->tempDir . '/' . Str::uuid();
            File::makeDirectory($extractDir);

            $zip = new ZipArchive();
            $zip->open($zipPath);
            $zip->extractTo($extractDir);
            $zip->close();

            // Copy files/ → project root
            $filesDir = $extractDir . '/files';
            if (is_dir($filesDir)) {
                $this->copyDirectory($filesDir, $this->basePath);
            }

            // Run new migrations
            $migrationsDir = $extractDir . '/migrations';
            if (is_dir($migrationsDir) && !empty($manifest['has_migrations'])) {
                // Copy migration files to database/migrations
                $targetMigrations = database_path('migrations');
                foreach (File::files($migrationsDir) as $file) {
                    File::copy($file->getPathname(), $targetMigrations . '/' . $file->getFilename());
                }
                Artisan::call('migrate', ['--force' => true]);
            }

            // Update CMS version in config
            $this->writeVersionToConfig($newVersion);

            // Clear all caches
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            Cache::flush();

            // Cleanup temp
            File::deleteDirectory($extractDir);
            File::delete($zipPath);

            // Mark success
            DB::table('cms_updates')->where('id', $updateId)->update([
                'status'     => 'success',
                'updated_at' => now(),
            ]);

            return ['success' => true, 'version' => $newVersion, 'update_id' => $updateId];

        } catch (\Throwable $e) {
            Log::error('CMS Update failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            DB::table('cms_updates')->where('id', $updateId)->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
                'updated_at'    => now(),
            ]);

            return ['success' => false, 'error' => $e->getMessage(), 'update_id' => $updateId];
        }
    }

    /* ─────────────────────────────────────────────────────────────
     |  Rollback
     * ─────────────────────────────────────────────────────────── */

    public function rollback(int $updateId): array
    {
        $update = DB::table('cms_updates')->find($updateId);
        if (!$update || empty($update->backup_path) || !file_exists($update->backup_path)) {
            return ['success' => false, 'error' => 'Backup file not found. Cannot rollback.'];
        }

        try {
            $zip = new ZipArchive();
            $zip->open($update->backup_path);
            $zip->extractTo($this->basePath);
            $zip->close();

            // Restore version
            if ($update->previous_version) {
                $this->writeVersionToConfig($update->previous_version);
            }

            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Cache::flush();

            DB::table('cms_updates')->where('id', $updateId)->update([
                'status'     => 'rolled_back',
                'updated_at' => now(),
            ]);

            return ['success' => true];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /* ─────────────────────────────────────────────────────────────
     |  History
     * ─────────────────────────────────────────────────────────── */

    public function history(int $limit = 20)
    {
        return DB::table('cms_updates')->orderByDesc('created_at')->limit($limit)->get();
    }

    /* ─────────────────────────────────────────────────────────────
     |  List Available Backups
     * ─────────────────────────────────────────────────────────── */

    public function listBackups(): array
    {
        if (!is_dir($this->backupDir)) return [];

        return collect(File::files($this->backupDir))
            ->sortByDesc(fn($f) => $f->getMTime())
            ->map(fn($f) => [
                'name'     => $f->getFilename(),
                'path'     => $f->getPathname(),
                'size'     => $this->humanSize($f->getSize()),
                'modified' => \Carbon\Carbon::createFromTimestamp($f->getMTime())->format('d M Y, h:i A'),
            ])->values()->toArray();
    }

    /* ─────────────────────────────────────────────────────────────
     |  Helpers
     * ─────────────────────────────────────────────────────────── */

    protected function addDirToZip(ZipArchive $zip, string $dir, string $prefix): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($files as $file) {
            $relative = $prefix . '/' . str_replace($dir . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $zip->addFile($file->getPathname(), str_replace('\\', '/', $relative));
        }
    }

    protected function copyDirectory(string $source, string $dest): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $target = $dest . '/' . $iterator->getSubPathname();
            if ($item->isDir()) {
                File::ensureDirectoryExists($target);
            } else {
                File::ensureDirectoryExists(dirname($target));
                File::copy($item->getPathname(), $target);
            }
        }
    }

    protected function writeVersionToConfig(string $version): void
    {
        $configPath = config_path('cms.php');
        $contents   = "<?php\nreturn [\n    'version' => '{$version}',\n];\n";
        File::put($configPath, $contents);
    }

    protected function humanSize(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}
