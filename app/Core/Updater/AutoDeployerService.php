<?php

namespace App\Core\Updater;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;
use ZipArchive;

/**
 * Production Transactional Auto-Deployer Service
 *
 * Implements protected transactional deployment lifecycle:
 * PREVIOUS WORKING VERSION ➔ VERIFIED RESTORE POINT ➔ APPLY UPDATE ➔ HEALTH CHECKS ➔ PASS: COMMIT / FAIL: AUTOMATIC ROLLBACK
 */
class AutoDeployerService
{
    protected string $laravelRoot;
    protected ?string $webRoot;
    protected string $phpBinary;
    protected string $gitBinary;
    protected string $backupDir;
    protected string $lockFile;
    protected string $deployLog;
    protected DeploymentHealthChecker $healthChecker;
    protected RollbackManager $rollbackManager;
    protected array $logs = [];

    public function __construct()
    {
        $this->laravelRoot     = $this->detectLaravelRoot();
        $this->webRoot         = $this->detectWebRoot();
        $this->phpBinary       = $this->detectPhpBinary();
        $this->gitBinary       = $this->detectGitBinary();
        $this->backupDir       = storage_path('backups/updates');
        $this->lockFile        = storage_path('framework/deployment.lock');
        $this->deployLog       = storage_path('logs/deployment.log');
        $this->healthChecker   = new DeploymentHealthChecker($this->laravelRoot, $this->webRoot);
        $this->rollbackManager = new RollbackManager($this->laravelRoot, $this->webRoot);
    }

    /**
     * Get auto-detected system info & update availability
     */
    public function getSystemInfo(): array
    {
        $updateStatus = $this->checkForRemoteUpdates();

        return [
            'laravel_root'     => $this->laravelRoot,
            'web_root'         => $this->webRoot ?? 'Unified (Inside Laravel root/public)',
            'is_split_public'  => $this->isPublicSplit(),
            'php_binary'       => $this->phpBinary,
            'git_binary'       => $this->gitBinary,
            'current_git_rev'  => $this->getCurrentGitRevision(),
            'current_git_sha'  => $this->getCurrentGitSha(),
            'update_available' => $updateStatus['update_available'] ?? false,
            'remote_commit'    => $updateStatus['remote_commit'] ?? null,
            'is_locked'        => file_exists($this->lockFile),
        ];
    }

    /**
     * Check GitHub for remote updates
     */
    public function checkForRemoteUpdates(string $repo = 'manishsaini10/prayaag', string $branch = 'main'): array
    {
        return Cache::remember('cms_remote_git_update_check', 30, function () use ($repo, $branch) {
            $localSha   = $this->getCurrentGitSha();
            $currentVer = config('cms.version', '1.3.2');

            // Calculate next target semantic version (e.g. 1.3.2 -> 1.3.3)
            $parts = explode('.', $currentVer);
            if (count($parts) === 3) {
                $parts[2] = ((int) $parts[2]) + 1;
                $predictedVer = implode('.', $parts);
            } else {
                $predictedVer = $currentVer . '.1';
            }

            try {
                $response = Http::timeout(4)
                    ->withHeaders(['User-Agent' => 'Prayaag-School-CMS'])
                    ->get("https://api.github.com/repos/{$repo}/commits/{$branch}");

                if ($response->successful()) {
                    $data      = $response->json();
                    $remoteSha = substr($data['sha'] ?? '', 0, 7);
                    $fullSha   = $data['sha'] ?? '';
                    $message   = $data['commit']['message'] ?? '';
                    $author    = $data['commit']['author']['name'] ?? 'Author';
                    $date      = $data['commit']['author']['date'] ?? '';

                    $isUpdateAvailable = !empty($remoteSha) && !empty($localSha) &&
                        !str_starts_with($localSha, $remoteSha) && !str_starts_with($fullSha, $localSha);

                    return [
                        'update_available' => $isUpdateAvailable,
                        'local_sha'        => $localSha,
                        'remote_sha'       => $remoteSha,
                        'target_version'   => 'v' . $predictedVer,
                        'remote_commit'    => [
                            'sha'     => $remoteSha,
                            'version' => 'v' . $predictedVer,
                            'message' => $message,
                            'author'  => $author,
                            'date'    => $date ? \Carbon\Carbon::parse($date)->diffForHumans() : '',
                        ],
                    ];
                }
            } catch (Throwable $e) {
                Log::warning('[AutoDeployer] GitHub update check warning: ' . $e->getMessage());
            }

            return [
                'update_available' => false,
                'local_sha'        => $localSha,
                'target_version'   => 'v' . $currentVer,
                'remote_commit'    => null,
            ];
        });
    }

    /**
     * Execute Transactional Deployment Pipeline
     */
    public function backupAndDeploy(string $branch = 'main', ?string $appliedBy = 'Admin'): array
    {
        $this->logs = [];
        $startTime  = microtime(true);
        $deploymentId = 'DEPLOY-' . date('Ymd-His') . '-' . substr(bin2hex(random_bytes(3)), 0, 6);
        $oldSha       = $this->getCurrentGitSha();
        $oldVersion   = config('cms.version', '1.0.0');

        $this->log("🚀 [Deployment: {$deploymentId}] Initiating transactional protected deployment...");

        // 1. Concurrency Protection (Lock)
        if (!$this->acquireLock($deploymentId)) {
            $msg = "Another deployment is already in progress. Please wait for it to complete.";
            $this->log("⚠ {$msg}");
            return [
                'success' => false,
                'status'  => 'locked',
                'message' => $msg,
                'logs'    => $this->logs,
            ];
        }

        $backupDir = null;
        $dbMigrated = false;
        $updateDbId = null;

        try {
            // Register initial record in cms_updates
            $updateDbId = $this->initUpdateRecord($deploymentId, $oldSha, $oldVersion, $branch, $appliedBy);

            // =================================================================
            // PHASE 1: Create Complete Structured Restore Point
            // =================================================================
            $this->updateStage($updateDbId, 'backing_up');
            $this->log("📦 [Phase 1] Creating verified pre-update restore point...");

            $backupDir = $this->createVerifiedRestorePoint($deploymentId, $oldVersion, $oldSha);
            $this->log("  ✔ Restore point created: " . basename($backupDir));

            // =================================================================
            // PHASE 2: Backup Verification (SHA-256 & Non-Empty Checks)
            // =================================================================
            $this->updateStage($updateDbId, 'backup_verified');
            $this->log("🔍 [Phase 2] Verifying restore point checksums and database dump...");
            $this->verifyRestorePoint($backupDir);
            $this->log("  ✔ Restore point verified with valid SHA-256 checksums.");

            // =================================================================
            // PHASE 3: Git Pull
            // =================================================================
            $this->updateStage($updateDbId, 'updating');
            $this->log("🔄 [Phase 3] Pulling latest code from origin/{$branch}...");
            $gitPulled = $this->runGitPull($branch);
            if (!$gitPulled) {
                throw new \RuntimeException("Git pull failed. Repository working tree preserved.");
            }
            $newSha = $this->getCurrentGitSha();
            $this->log("  ✔ Pulled commit: {$newSha}");

            // =================================================================
            // PHASE 4: Composer Dependencies
            // =================================================================
            $this->updateStage($updateDbId, 'composer_installing');
            $this->log("📦 [Phase 4] Installing Composer dependencies (--no-dev --optimize-autoloader)...");
            $this->runComposer();
            $this->log("  ✔ Composer dependencies optimized.");

            // =================================================================
            // PHASE 5: Database Migrations
            // =================================================================
            $this->updateStage($updateDbId, 'migrating');
            $this->log("🗄️ [Phase 5] Running database migrations (php artisan migrate --force)...");
            $this->runMigrations();
            $dbMigrated = true;
            $this->log("  ✔ Database migrations completed.");

            // =================================================================
            // PHASE 6: Public & Vite Asset Synchronization
            // =================================================================
            $this->updateStage($updateDbId, 'syncing_assets');
            $this->log("📂 [Phase 6] Synchronizing public files & Vite build to Web Root...");
            $this->syncPublicAndViteAssets();
            $this->log("  ✔ Public assets synchronized.");

            // =================================================================
            // PHASE 7: Runtime Preparation & Cache Rebuild
            // =================================================================
            $this->updateStage($updateDbId, 'caching');
            $this->log("🧹 [Phase 7] Rebuilding production runtime caches...");
            $this->prepareRuntimeAndCaches();
            $this->log("  ✔ Caches rebuilt.");

            // =================================================================
            // PHASE 8: Comprehensive Multi-Tier Health Checks
            // =================================================================
            $this->updateStage($updateDbId, 'health_check');
            $this->log("🩺 [Phase 8] Executing comprehensive post-deployment health check suite...");
            $healthResult = $this->healthChecker->runFullHealthCheck(maxRetries: 3, timeoutSeconds: 10);

            $duration = round(microtime(true) - $startTime, 2);

            // =================================================================
            // EVALUATION: PASS or TRIGGER AUTOMATIC ROLLBACK
            // =================================================================
            if ($healthResult['status'] === 'healthy') {
                $this->bumpVersionAfterDeploy($branch);
                $newVersion = config('cms.version', '1.3.2');

                $this->finalizeSuccessRecord($updateDbId, $newSha, $newVersion, $duration, $healthResult, $backupDir);
                $this->cleanupOldBackups(retainCount: 5);
                Cache::forget('cms_remote_git_update_check');
                $this->releaseLock();

                $msg = "UPDATE SUCCESSFUL: Deployed {$newVersion} ({$newSha}) in {$duration}s with all health checks passed.";
                $this->log("✅ {$msg}");
                $this->writeAuditLog("SUCCESS", $deploymentId, $msg);

                return [
                    'success'       => true,
                    'status'        => 'success',
                    'message'       => $msg,
                    'deployment_id' => $deploymentId,
                    'duration'      => $duration,
                    'health'        => $healthResult,
                    'logs'          => $this->logs,
                    'backup_path'   => $backupDir,
                ];
            }

            // Health Check Failed -> Throw Exception to trigger rollback
            $healthErrors = implode('; ', $healthResult['errors'] ?? ['Unknown health check failure']);
            throw new \RuntimeException("Post-deployment health checks failed: {$healthErrors}");

        } catch (Throwable $e) {
            $duration = round(microtime(true) - $startTime, 2);
            $errMsg = $e->getMessage();
            $this->log("❌ [DEPLOYMENT FAILED] {$errMsg}");
            $this->log("🚨 [TRIGGERING AUTOMATIC ROLLBACK] Restoring previous working state...");

            $this->updateStage($updateDbId, 'rolling_back');

            // Trigger Automatic Rollback
            $rollbackResult = [];
            if ($backupDir && file_exists($backupDir)) {
                $rollbackResult = $this->rollbackManager->executeRollback($backupDir, $deploymentId, $dbMigrated);
            }

            $this->releaseLock();

            $isRollbackVerified = !empty($rollbackResult['health_verified']);
            $finalStatus = $isRollbackVerified ? 'rollback_verified' : 'rollback_failed';

            $this->finalizeFailureRecord($updateDbId, $errMsg, $duration, $finalStatus, $rollbackResult);
            $this->writeAuditLog("FAILED_ROLLBACK_" . ($isRollbackVerified ? 'SUCCESS' : 'FAILED'), $deploymentId, $errMsg);

            return [
                'success'         => false,
                'status'          => $finalStatus,
                'message'         => $errMsg,
                'rollback'        => $rollbackResult,
                'deployment_id'   => $deploymentId,
                'duration'        => $duration,
                'logs'            => array_merge($this->logs, $rollbackResult['logs'] ?? []),
                'backup_path'     => $backupDir,
            ];
        }
    }

    /**
     * Create Complete Verified Restore Point Folder
     */
    protected function createVerifiedRestorePoint(string $deploymentId, string $version, string $sha): string
    {
        File::ensureDirectoryExists($this->backupDir);
        $pointDir = $this->backupDir . '/backup-' . $deploymentId;
        File::ensureDirectoryExists($pointDir);

        // 1. Metadata.json
        $metadata = [
            'deployment_id' => $deploymentId,
            'version'       => $version,
            'commit_sha'    => $sha,
            'timestamp'     => date('Y-m-d H:i:s'),
            'web_root'      => $this->webRoot,
            'laravel_root'  => $this->laravelRoot,
        ];
        file_put_contents($pointDir . '/metadata.json', json_encode($metadata, JSON_PRETTY_PRINT));

        // 2. Application Archive (tar.gz)
        $appArchive = $pointDir . '/application.tar.gz';
        $cmd = "tar -czf \"{$appArchive}\" -C \"{$this->laravelRoot}\" --exclude='vendor' --exclude='node_modules' --exclude='storage' app config database resources routes composer.json composer.lock 2>&1";
        $this->execCommand($cmd);

        // 3. Public Web Assets Archive (tar.gz)
        $publicArchive = $pointDir . '/public.tar.gz';
        $cmd = "tar -czf \"{$publicArchive}\" -C \"{$this->laravelRoot}/public\" build css js images site.css admin.css robots.txt 2>&1";
        $this->execCommand($cmd);

        // 4. .env Backup
        if (file_exists($this->laravelRoot . '/.env')) {
            File::copy($this->laravelRoot . '/.env', $pointDir . '/env.backup');
        }

        // 5. Complete Database Snapshot (Compressed .sql.gz)
        $dbDumpPath = $pointDir . '/database.sql';
        $dbDump = $this->exportDatabaseSql();
        file_put_contents($dbDumpPath, $dbDump);
        file_put_contents($pointDir . '/database.sql.gz', gzencode($dbDump, 9));
        @unlink($dbDumpPath);

        // 6. SHA-256 Checksums
        $checksums = [];
        foreach (['metadata.json', 'application.tar.gz', 'public.tar.gz', 'env.backup', 'database.sql.gz'] as $file) {
            $fPath = $pointDir . '/' . $file;
            if (file_exists($fPath)) {
                $checksums[] = hash_file('sha256', $fPath) . '  ' . $file;
            }
        }
        file_put_contents($pointDir . '/checksums.sha256', implode("\n", $checksums) . "\n");

        return $pointDir;
    }

    /**
     * Verify Restore Point Integrity
     */
    protected function verifyRestorePoint(string $pointDir): void
    {
        $required = ['metadata.json', 'application.tar.gz', 'public.tar.gz', 'database.sql.gz', 'checksums.sha256'];
        foreach ($required as $req) {
            $f = $pointDir . '/' . $req;
            if (!file_exists($f) || filesize($f) < 10) {
                throw new \RuntimeException("Restore point verification failed: {$req} is missing or empty.");
            }
        }

        // Validate Checksums
        $checksumFile = $pointDir . '/checksums.sha256';
        $lines = file($checksumFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $parts = preg_split('/\s+/', trim($line), 2);
            if (count($parts) === 2) {
                [$hash, $fn] = $parts;
                $actual = hash_file('sha256', $pointDir . '/' . $fn);
                if ($actual !== $hash) {
                    throw new \RuntimeException("Checksum verification mismatch for {$fn}!");
                }
            }
        }
    }

    /**
     * Run Git Pull
     */
    protected function runGitPull(string $branch): bool
    {
        $cmd = "cd \"{$this->laravelRoot}\" && {$this->gitBinary} fetch origin {$branch} 2>&1 && {$this->gitBinary} pull origin {$branch} 2>&1";
        $res = $this->execCommand($cmd);
        $this->log("  ↳ " . trim($res));
        return !str_contains(strtolower($res), 'fatal:') && !str_contains(strtolower($res), 'error:');
    }

    /**
     * Run Composer Install
     */
    protected function runComposer(): void
    {
        $cmd = "cd \"{$this->laravelRoot}\" && composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader 2>&1 && composer dump-autoload -o --no-interaction 2>&1";
        $res = $this->execCommand($cmd);
        $this->log("  ↳ " . trim($res));
    }

    /**
     * Run Migrations
     */
    protected function runMigrations(): void
    {
        Artisan::call('migrate', ['--force' => true]);
        $out = trim(Artisan::output());
        $this->log("  ↳ " . ($out ?: 'No new migrations.'));
    }

    /**
     * Sync Public & Vite Assets
     */
    protected function syncPublicAndViteAssets(): void
    {
        if (!$this->webRoot || $this->webRoot === ($this->laravelRoot . '/public') || !is_dir($this->webRoot)) {
            return;
        }

        // Sync build folder
        $srcBuild = $this->laravelRoot . '/public/build';
        $dstBuild = $this->webRoot . '/build';
        if (is_dir($srcBuild)) {
            File::ensureDirectoryExists($dstBuild);
            File::copyDirectory($srcBuild, $dstBuild);
            $this->log("  ↳ Synced build assets to {$dstBuild}");
        }

        // Sync css, js, images
        foreach (['css', 'js', 'images', 'fonts'] as $dir) {
            $s = $this->laravelRoot . '/public/' . $dir;
            $d = $this->webRoot . '/' . $dir;
            if (is_dir($s)) {
                File::ensureDirectoryExists($d);
                File::copyDirectory($s, $d);
            }
        }

        // Sync root assets
        foreach (['site.css', 'admin.css', 'robots.txt', 'favicon.ico', 'deploy.php'] as $file) {
            $sf = $this->laravelRoot . '/public/' . $file;
            $df = $this->webRoot . '/' . $file;
            if (file_exists($sf)) {
                File::copy($sf, $df);
            }
        }

        // Storage Symlink
        if (!is_link($this->webRoot . '/storage') && !is_dir($this->webRoot . '/storage')) {
            @symlink(storage_path('app/public'), $this->webRoot . '/storage');
        }
    }

    /**
     * Runtime Prep & Cache Rebuild
     */
    protected function prepareRuntimeAndCaches(): void
    {
        File::ensureDirectoryExists($this->laravelRoot . '/bootstrap/cache');
        File::ensureDirectoryExists(storage_path('framework/cache'));
        File::ensureDirectoryExists(storage_path('framework/sessions'));
        File::ensureDirectoryExists(storage_path('framework/views'));
        File::ensureDirectoryExists(storage_path('logs'));

        Artisan::call('storage:link');
        Artisan::call('optimize:clear');
        Artisan::call('config:cache');
        Artisan::call('view:cache');
        try {
            Artisan::call('route:cache');
        } catch (Throwable $e) {
            $this->log("  ⚠ Route cache notice: " . $e->getMessage());
        }
    }

    /**
     * Clean old backups, keeping recent 3-5 restore points
     */
    protected function cleanupOldBackups(int $retainCount = 5): void
    {
        try {
            $all = glob($this->backupDir . '/backup-*', GLOB_ONLYDIR);
            if (count($all) > $retainCount) {
                usort($all, fn($a, $b) => filemtime($b) <=> filemtime($a));
                $toDelete = array_slice($all, $retainCount);
                foreach ($toDelete as $oldDir) {
                    File::deleteDirectory($oldDir);
                    $this->log("  ↳ Purged older restore point: " . basename($oldDir));
                }
            }
        } catch (Throwable $e) {
            // Ignore cleanup errors
        }
    }

    /**
     * Legacy helper: Add directory recursively to ZipArchive
     */
    public function addDirToZip(ZipArchive $zip, string $dir, string $prefix): void
    {
        if (!is_dir($dir)) return;

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = $prefix . '/' . substr($filePath, strlen(realpath($dir)) + 1);
                $zip->addFile($filePath, str_replace('\\', '/', $relativePath));
            }
        }
    }

    /**
     * Legacy pre-update backup helper
     */
    public function createPreUpdateBackup(string $version = 'pre-deploy'): string
    {
        $deploymentId = 'DEPLOY-' . date('Ymd-His') . '-' . substr(bin2hex(random_bytes(3)), 0, 6);
        $sha = $this->getCurrentGitSha();
        return $this->createVerifiedRestorePoint($deploymentId, $version, $sha);
    }


    /**
     * Export complete database SQL via PDO
     */
    protected function exportDatabaseSql(): string
    {
        $tables = DB::select('SHOW TABLES');
        if (empty($tables)) return '';

        $out = "-- Database Snapshot before update\n-- Date: " . date('Y-m-d H:i:s') . "\nSET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $tableObj) {
            $tableArr = (array) $tableObj;
            $tableName = reset($tableArr);

            if (in_array($tableName, ['activity_logs', 'sessions', 'cache'])) continue;

            $createRes = DB::select("SHOW CREATE TABLE `{$tableName}`");
            if (!empty($createRes)) {
                $createArr = (array) $createRes[0];
                $out .= "DROP TABLE IF EXISTS `{$tableName}`;\n" . ($createArr['Create Table'] ?? '') . ";\n\n";
            }

            $rows = DB::table($tableName)->get();
            if ($rows->isNotEmpty()) {
                foreach ($rows as $row) {
                    $rowArr = (array) $row;
                    $cols = array_map(fn($c) => "`{$c}`", array_keys($rowArr));
                    $vals = array_map(function ($v) {
                        if (is_null($v)) return 'NULL';
                        return "'" . addslashes((string)$v) . "'";
                    }, array_values($rowArr));

                    $out .= "INSERT INTO `{$tableName}` (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ");\n";
                }
                $out .= "\n";
            }
        }

        $out .= "SET FOREIGN_KEY_CHECKS=1;\n";
        return $out;
    }

    /**
     * Bumps semantic version in config/cms.php
     */
    public function bumpVersionAfterDeploy(string $branch = 'main'): void
    {
        try {
            $configFile = $this->laravelRoot . '/config/cms.php';
            $currentVer = config('cms.version', '1.3.1');
            $parts      = explode('.', $currentVer);

            if (count($parts) === 3) {
                $parts[2] = ((int) $parts[2]) + 1;
                $newVer = implode('.', $parts);
            } else {
                $newVer = $currentVer . '.1';
            }

            $newSha = $this->getCurrentGitSha();
            $now    = date('Y-m-d H:i:s');

            $phpContent = <<<PHP
<?php

return [
    /*
     |--------------------------------------------------------------------------
     | CMS Version & Release Tracking
     |--------------------------------------------------------------------------
     | Automatically updated by AutoDeployer on deploy.
     */
    'version'     => '{$newVer}',
    'build'       => '{$newSha}',
    'branch'      => '{$branch}',
    'released_at' => '{$now}',
    'changelog'   => 'Automated update applied via 1-Click Deployer',
];

PHP;
            File::put($configFile, $phpContent);
            $this->log("🔖 Version updated to v{$newVer} (Build: {$newSha})");
        } catch (Throwable $e) {
            $this->log("  ⚠ Version bump notice: " . $e->getMessage());
        }
    }

    /**
     * Concurrency Lock Acquisition
     */
    protected function acquireLock(string $id): bool
    {
        if (file_exists($this->lockFile)) {
            $mtime = filemtime($this->lockFile);
            if (time() - $mtime > 300) { // 5 minutes stale lock expiry
                @unlink($this->lockFile);
            } else {
                return false;
            }
        }

        File::put($this->lockFile, json_encode(['id' => $id, 'time' => date('Y-m-d H:i:s')]));
        return true;
    }

    protected function releaseLock(): void
    {
        if (file_exists($this->lockFile)) {
            @unlink($this->lockFile);
        }
    }

    // Database record helpers
    protected function initUpdateRecord(string $deployId, string $sha, string $ver, string $branch, string $by): int
    {
        try {
            return DB::table('cms_updates')->insertGetId([
                'deployment_id'    => $deployId,
                'version'          => 'Deploying...',
                'previous_version' => $ver,
                'previous_commit'  => $sha,
                'package_name'     => "origin/{$branch}",
                'changelog'        => 'Transactional deployment pipeline',
                'status'           => 'pending',
                'stage'            => 'backing_up',
                'applied_by'       => $by,
                'applied_at'       => now(),
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        } catch (Throwable $e) {
            return 0;
        }
    }

    protected function updateStage(int $id, string $stage): void
    {
        if (!$id) return;
        try {
            DB::table('cms_updates')->where('id', $id)->update(['stage' => $stage, 'updated_at' => now()]);
        } catch (Throwable $e) {}
    }

    protected function finalizeSuccessRecord(int $id, string $newSha, string $newVer, float $duration, array $health, string $backup): void
    {
        if (!$id) return;
        try {
            DB::table('cms_updates')->where('id', $id)->update([
                'version'             => $newVer,
                'new_commit'          => $newSha,
                'status'              => 'success',
                'stage'               => 'success',
                'duration'            => $duration,
                'backup_path'         => $backup,
                'health_check_result' => json_encode($health),
                'updated_at'          => now(),
            ]);
        } catch (Throwable $e) {}
    }

    protected function finalizeFailureRecord(int $id, string $error, float $duration, string $stage, array $rollback): void
    {
        if (!$id) return;
        try {
            DB::table('cms_updates')->where('id', $id)->update([
                'status'              => $stage === 'rollback_verified' ? 'rolled_back' : 'failed',
                'stage'               => $stage,
                'error_message'       => $error,
                'duration'            => $duration,
                'rollback_status'     => $stage,
                'health_check_result' => json_encode($rollback),
                'updated_at'          => now(),
            ]);
        } catch (Throwable $e) {}
    }

    protected function writeAuditLog(string $event, string $id, string $details): void
    {
        $entry = "[" . date('Y-m-d H:i:s') . "] [{$event}] [{$id}] {$details}\n";
        @file_put_contents($this->deployLog, $entry, FILE_APPEND);
    }

    public function getCurrentGitRevision(): string
    {
        $cmd = "cd \"{$this->laravelRoot}\" && {$this->gitBinary} log -1 --pretty=format:\"%h - %s (%ci)\" 2>&1";
        $res = trim($this->execCommand($cmd));
        if (!empty($res) && !str_contains(strtolower($res), 'fatal') && !str_contains(strtolower($res), 'not a git')) {
            return $res;
        }

        $ver   = config('cms.version', '1.3.1');
        $build = config('cms.build', 'a4623f7');
        $date  = config('cms.released_at', date('Y-m-d H:i'));

        return "v{$ver} · Build {$build} ({$date})";
    }

    public function getCurrentGitSha(): string
    {
        $cmd = "cd \"{$this->laravelRoot}\" && {$this->gitBinary} rev-parse --short HEAD 2>&1";
        $res = trim($this->execCommand($cmd));
        return (!empty($res) && strlen($res) <= 12 && ctype_alnum($res)) ? $res : config('cms.build', 'a4623f7');
    }

    protected function detectLaravelRoot(): string
    {
        return function_exists('base_path') ? base_path() : getcwd();
    }

    protected function detectWebRoot(): ?string
    {
        $configFile = $this->laravelRoot . '/.deploy-config';
        if (file_exists($configFile)) {
            $lines = file($configFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (str_starts_with(trim($line), 'WEB_ROOT=')) {
                    $val = trim(explode('=', $line, 2)[1] ?? '', " \t\n\r\0\x0B\"'");
                    if (!empty($val) && is_dir($val)) return $val;
                }
            }
        }
        return is_dir(dirname($this->laravelRoot) . '/public_html')
            ? dirname($this->laravelRoot) . '/public_html'
            : public_path();
    }

    protected function isPublicSplit(): bool
    {
        if (!$this->webRoot) return false;
        return realpath($this->laravelRoot . '/public') !== realpath($this->webRoot);
    }

    protected function detectPhpBinary(): string
    {
        return defined('PHP_BINARY') && file_exists(PHP_BINARY) ? PHP_BINARY : 'php';
    }

    protected function detectGitBinary(): string
    {
        return 'git';
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
        Log::info('[AutoDeployer] ' . $msg);
    }
}
