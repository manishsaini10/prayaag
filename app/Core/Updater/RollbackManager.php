<?php

namespace App\Core\Updater;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Throwable;
use ZipArchive;

/**
 * Production Automated Rollback Engine
 *
 * Atomically restores the application to a verified pre-update restore point:
 * 1. Restores Application Source Code (app, config, routes, database, resources)
 * 2. Restores Public Web Assets & Vite build files to WEB_ROOT
 * 3. Restores .env configuration (protected, non-git)
 * 4. Restores Database Snapshot if migrations were applied
 * 5. Rebuilds runtime caches
 * 6. Executes DeploymentHealthChecker again to verify restored health
 */
class RollbackManager
{
    protected string $projectRoot;
    protected string $publicDir;
    protected ?string $webRoot;
    protected DeploymentHealthChecker $healthChecker;
    protected array $logs = [];

    public function __construct(?string $projectRoot = null, ?string $webRoot = null)
    {
        $this->projectRoot   = $projectRoot ?: base_path();
        $this->publicDir     = $this->projectRoot . '/public';
        $this->webRoot       = $webRoot ?: $this->detectWebRoot();
        $this->healthChecker = new DeploymentHealthChecker($this->projectRoot, $this->webRoot);
    }

    /**
     * Execute full atomic rollback to specified restore point
     */
    public function executeRollback(string $backupPath, ?string $deploymentId = null, bool $databaseWasMigrated = false): array
    {
        $this->logs = [];
        $startTime  = microtime(true);
        $this->log("↩ [Rollback] Starting automated rollback process...");
        $this->log("  ↳ Target Restore Point: " . basename($backupPath));

        // Step 1: Verify Restore Point Integrity
        if (!file_exists($backupPath)) {
            $err = "Restore point does not exist: {$backupPath}";
            $this->log("  ❌ {$err}");
            return [
                'success'         => false,
                'status'          => 'rollback_failed',
                'error'           => $err,
                'logs'            => $this->logs,
                'health_verified' => false,
            ];
        }

        // Step 2: Restore Source & Public Files
        try {
            if (is_dir($backupPath)) {
                $this->restoreFromDirectory($backupPath, $databaseWasMigrated);
            } else {
                $this->restoreFromZip($backupPath, $databaseWasMigrated);
            }
            $this->log("  ✔ Files restored from backup.");
        } catch (Throwable $e) {
            $err = "File restoration failed: " . $e->getMessage();
            $this->log("  ❌ {$err}");
            return [
                'success'         => false,
                'status'          => 'rollback_failed',
                'error'           => $err,
                'logs'            => $this->logs,
                'health_verified' => false,
            ];
        }

        // Step 3: Rebuild Runtime Caches
        $this->rebuildCaches();

        // Step 4: Run Health Check on Restored Version
        $this->log("🔍 [Rollback] Running health check suite on restored application...");
        $healthResult = $this->healthChecker->runFullHealthCheck(maxRetries: 2, timeoutSeconds: 8);

        $duration = round(microtime(true) - $startTime, 2);

        if ($healthResult['status'] === 'healthy') {
            $msg = "ROLLBACK SUCCESSFUL: Previous version restored and verified healthy in {$duration}s.";
            $this->log("  ✅ {$msg}");

            $this->recordRollbackStatus($deploymentId, 'rollback_verified', $healthResult);

            return [
                'success'         => true,
                'status'          => 'rollback_verified',
                'message'         => $msg,
                'health'          => $healthResult,
                'duration'        => $duration,
                'logs'            => $this->logs,
                'health_verified' => true,
            ];
        }

        $msg = "CRITICAL: ROLLBACK FAILED. Health checks failed after restoration: " . json_encode($healthResult['errors']);
        $this->log("  ❌ {$msg}");

        $this->recordRollbackStatus($deploymentId, 'rollback_failed', $healthResult);

        return [
            'success'         => false,
            'status'          => 'rollback_failed',
            'error'           => $msg,
            'health'          => $healthResult,
            'duration'        => $duration,
            'logs'            => $this->logs,
            'health_verified' => false,
        ];
    }

    /**
     * Restore from structured backup directory
     */
    protected function restoreFromDirectory(string $dir, bool $restoreDatabase = false): void
    {
        // 1. Verify checksums if available
        $checksumFile = $dir . '/checksums.sha256';
        if (file_exists($checksumFile)) {
            $this->log("  ↳ Validating SHA-256 backup checksums...");
            $this->verifyChecksums($dir, $checksumFile);
        }

        // 2. Restore Application Archive (.zip or .tar.gz)
        $appZip = $dir . '/application.zip';
        $appTar = $dir . '/application.tar.gz';
        if (file_exists($appZip)) {
            $this->log("  ↳ Extracting application ZIP archive...");
            $zip = new ZipArchive();
            if ($zip->open($appZip) === true) {
                $zip->extractTo($this->projectRoot);
                $zip->close();
            }
        } elseif (file_exists($appTar)) {
            $this->log("  ↳ Extracting application tar archive...");
            $cmd = "tar -xzf \"{$appTar}\" -C \"{$this->projectRoot}\" 2>&1";
            $this->execCommand($cmd);
        }

        // 3. Restore Public Web Assets (.zip or .tar.gz)
        $pubZip = $dir . '/public.zip';
        $pubTar = $dir . '/public.tar.gz';
        if (file_exists($pubZip)) {
            $this->log("  ↳ Extracting public web assets ZIP archive...");
            $zip = new ZipArchive();
            if ($zip->open($pubZip) === true) {
                $zip->extractTo($this->publicDir);
                $zip->close();
            }
        } elseif (file_exists($pubTar)) {
            $this->log("  ↳ Extracting public web assets tar archive...");
            $cmd = "tar -czf \"{$pubTar}\" -C \"{$this->publicDir}\" 2>&1";
            $this->execCommand($cmd);
        }

        // Sync to WEB_ROOT if distinct
        if ($this->webRoot && $this->webRoot !== $this->publicDir && is_dir($this->webRoot)) {
            $this->log("  ↳ Syncing restored assets to WEB_ROOT ({$this->webRoot})...");
            if (is_dir($this->publicDir . '/build')) {
                File::ensureDirectoryExists($this->webRoot . '/build');
                File::copyDirectory($this->publicDir . '/build', $this->webRoot . '/build');
            }
        }

        // 4. Restore .env backup if present
        $envBackup = $dir . '/env.backup';
        if (file_exists($envBackup)) {
            $this->log("  ↳ Restoring .env configuration...");
            File::copy($envBackup, $this->projectRoot . '/.env');
        }

        // 5. Restore Database Snapshot if required
        if ($restoreDatabase) {
            $sqlFile = $dir . '/database.sql';
            $sqlGz   = $dir . '/database.sql.gz';

            if (file_exists($sqlGz)) {
                $this->log("  ↳ Uncompressing and restoring MySQL database snapshot...");
                $uncompressed = $dir . '/temp_restore_db.sql';
                file_put_contents($uncompressed, gzdecode(file_get_contents($sqlGz)));
                $this->restoreDatabaseSql($uncompressed);
                @unlink($uncompressed);
            } elseif (file_exists($sqlFile)) {
                $this->log("  ↳ Restoring MySQL database snapshot...");
                $this->restoreDatabaseSql($sqlFile);
            }
        }
    }

    /**
     * Restore from single ZIP restore point
     */
    protected function restoreFromZip(string $zipPath, bool $restoreDatabase = false): void
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new \RuntimeException("Cannot open backup archive: {$zipPath}");
        }

        // Check if database_snapshot.sql exists inside zip
        $sqlContent = $zip->getFromName('database_snapshot.sql');

        // Extract files to project root
        $zip->extractTo($this->projectRoot);
        $zip->close();

        // If database was migrated, execute database snapshot
        if ($restoreDatabase && $sqlContent) {
            $this->log("  ↳ Restoring database snapshot from backup archive...");
            $tmpSql = storage_path('app/temp_db_restore.sql');
            file_put_contents($tmpSql, $sqlContent);
            $this->restoreDatabaseSql($tmpSql);
            @unlink($tmpSql);
        }

        // Sync public build to WEB_ROOT if split
        if ($this->webRoot && $this->webRoot !== $this->publicDir && is_dir($this->webRoot)) {
            if (is_dir($this->publicDir . '/build')) {
                File::ensureDirectoryExists($this->webRoot . '/build');
                File::copyDirectory($this->publicDir . '/build', $this->webRoot . '/build');
            }
        }
    }

    /**
     * Restore database SQL dump via PDO
     */
    protected function restoreDatabaseSql(string $sqlPath): void
    {
        if (!file_exists($sqlPath) || filesize($sqlPath) < 10) {
            $this->log("  ⚠ Database restore skipped: SQL file missing or empty.");
            return;
        }

        try {
            DB::unprepared("SET FOREIGN_KEY_CHECKS = 0;");
            $sql = file_get_contents($sqlPath);
            DB::unprepared($sql);
            DB::unprepared("SET FOREIGN_KEY_CHECKS = 1;");
            $this->log("  ✔ Database snapshot executed and restored successfully.");
        } catch (Throwable $e) {
            $this->log("  ⚠ Database restore error: " . $e->getMessage());
        }
    }

    /**
     * Verify SHA-256 Checksums
     */
    protected function verifyChecksums(string $dir, string $checksumFile): void
    {
        $lines = file($checksumFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $parts = preg_split('/\s+/', trim($line), 2);
            if (count($parts) === 2) {
                [$expectedHash, $filename] = $parts;
                $targetFile = $dir . '/' . basename($filename);
                if (file_exists($targetFile)) {
                    $actualHash = hash_file('sha256', $targetFile);
                    if ($actualHash !== $expectedHash) {
                        throw new \RuntimeException("Checksum mismatch for {$filename}! Backup is corrupted.");
                    }
                }
            }
        }
        $this->log("  ✔ SHA-256 checksums verified valid.");
    }

    /**
     * Rebuild Runtime Caches
     */
    protected function rebuildCaches(): void
    {
        $this->log("  ↳ Rebuilding runtime caches...");
        try {
            Artisan::call('storage:link');
            Artisan::call('optimize:clear');
            Artisan::call('config:cache');
            Artisan::call('view:cache');
            Artisan::call('route:cache');
        } catch (Throwable $e) {
            $this->log("  ⚠ Cache rebuild note: " . $e->getMessage());
        }
    }

    /**
     * Record rollback outcome in cms_updates table
     */
    protected function recordRollbackStatus(?string $deploymentId, string $status, array $health): void
    {
        if (!$deploymentId) return;

        try {
            DB::table('cms_updates')
                ->where('deployment_id', $deploymentId)
                ->orWhere('id', $deploymentId)
                ->update([
                    'status'              => $status === 'rollback_verified' ? 'rolled_back' : 'failed',
                    'stage'               => $status,
                    'rollback_status'     => $status,
                    'health_check_result' => json_encode($health),
                    'updated_at'          => now(),
                ]);
        } catch (Throwable $e) {
            // Ignore DB log failure
        }
    }

    protected function detectWebRoot(): ?string
    {
        $configFile = $this->projectRoot . '/.deploy-config';
        if (file_exists($configFile)) {
            $lines = file($configFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (str_starts_with(trim($line), 'WEB_ROOT=')) {
                    $val = trim(explode('=', $line, 2)[1] ?? '', " \t\n\r\0\x0B\"'");
                    if (!empty($val) && is_dir($val)) {
                        return $val;
                    }
                }
            }
        }

        return is_dir(dirname($this->projectRoot) . '/public_html')
            ? dirname($this->projectRoot) . '/public_html'
            : $this->publicDir;
    }

    protected function execCommand(string $cmd): string
    {
        $output = '';
        if (function_exists('shell_exec')) {
            $output = (string) shell_exec($cmd);
        } elseif (function_exists('exec')) {
            exec($cmd, $lines);
            $output = implode("\n", $lines);
        }
        return $output;
    }

    protected function log(string $msg): void
    {
        $entry = "[" . date('Y-m-d H:i:s') . "] " . $msg;
        $this->logs[] = $entry;
        Log::info('[RollbackManager] ' . $msg);
    }
}
