<?php

namespace App\Core\Updater;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ZipArchive;

/**
 * Smart Auto-Detecting Deployer Service
 * 
 * Automatically detects:
 * 1. Laravel root directory (where artisan / app lives)
 * 2. Public web root (public_html, htdocs, public, www)
 * 3. Whether public directory is separated from core
 * 4. PHP and Git executable paths
 * 5. Checks GitHub repository for new commits/releases
 * 6. Creates automatic pre-update backups before deploying
 */
class AutoDeployerService
{
    protected string $laravelRoot;
    protected ?string $webRoot;
    protected string $phpBinary;
    protected string $gitBinary;
    protected string $backupDir;
    protected array $logs = [];

    public function __construct()
    {
        $this->laravelRoot = $this->detectLaravelRoot();
        $this->webRoot     = $this->detectWebRoot();
        $this->phpBinary   = $this->detectPhpBinary();
        $this->gitBinary   = $this->detectGitBinary();
        $this->backupDir   = storage_path('backups/updates');
    }

    /**
     * Get auto-detected system paths summary
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
        ];
    }

    /**
     * Check GitHub for new commits on the repository
     */
    public function checkForRemoteUpdates(string $repo = 'manishsaini10/prayaag', string $branch = 'main'): array
    {
        return Cache::remember('cms_remote_git_update_check', 30, function () use ($repo, $branch) {
            $localSha = $this->getCurrentGitSha();
            $localRev = $this->getCurrentGitRevision();

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

                    $isUpdateAvailable = !empty($remoteSha) && !empty($localSha) && !str_starts_with($localSha, $remoteSha) && !str_starts_with($fullSha, $localSha);

                    return [
                        'update_available' => $isUpdateAvailable,
                        'local_sha'        => $localSha,
                        'remote_sha'       => $remoteSha,
                        'remote_commit'    => [
                            'sha'     => $remoteSha,
                            'message' => $message,
                            'author'  => $author,
                            'date'    => $date ? \Carbon\Carbon::parse($date)->diffForHumans() : '',
                        ],
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning('[AutoDeployer] GitHub update check warning: ' . $e->getMessage());
            }

            return [
                'update_available' => false,
                'local_sha'        => $localSha,
                'remote_commit'    => null,
            ];
        });
    }

    /**
     * Execute full Backup + Deploy pipeline
     */
    public function backupAndDeploy(string $branch = 'main', ?string $appliedBy = 'Admin'): array
    {
        $this->logs = [];
        $startTime  = microtime(true);

        $this->log("📦 Step 1: Taking automatic pre-update backup of core files...");
        $backupPath = null;
        try {
            $backupPath = $this->createPreUpdateBackup();
            $this->log("  ↳ Backup saved safely: " . basename($backupPath));
        } catch (\Throwable $e) {
            $this->log("  ⚠ Backup warning: " . $e->getMessage() . " (Continuing deploy...)");
        }

        $this->log("🚀 Step 2: Executing deployment pipeline from origin/{$branch}...");
        $deployResult = $this->deploy($branch);

        // Record in Database history
        try {
            DB::table('cms_updates')->insert([
                'version'          => 'Git (' . $this->getCurrentGitSha() . ')',
                'previous_version' => 'Git',
                'package_name'     => 'GitHub Auto-Sync (origin/' . $branch . ')',
                'changelog'        => $deployResult['message'] ?? 'Git sync update',
                'status'           => $deployResult['success'] ? 'success' : 'failed',
                'applied_by'       => $appliedBy,
                'error_message'    => $deployResult['success'] ? null : implode("\n", $deployResult['logs']),
                'backup_path'      => $backupPath,
                'applied_at'       => now(),
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        } catch (\Throwable $e) {
            // ignore if table not yet migrated
        }

        // Clear update check cache so blinking badge turns off immediately
        Cache::forget('cms_remote_git_update_check');

        $deployResult['backup_path'] = $backupPath;
        return $deployResult;
    }

    /**
     * Execute automated deployment pipeline
     */
    public function deploy(string $branch = 'main'): array
    {
        $startTime = microtime(true);

        $this->log("📍 Detected Laravel Root: {$this->laravelRoot}");
        $this->log("🌐 Detected Public Web Root: " . ($this->webRoot ?: 'Unified'));

        // Step 1: Git Pull
        $gitSuccess = $this->runGitPull($branch);
        if (!$gitSuccess) {
            return [
                'success' => false,
                'message' => 'Git pull failed. Check logs for details.',
                'logs'    => $this->logs,
            ];
        }

        // Step 2: Sync Public Assets if split (e.g. public_html)
        if ($this->isPublicSplit() && $this->webRoot) {
            $this->syncPublicAssets();
        }

        // Step 3: Run Migrations
        $this->runMigrations();

        // Step 4: Bump Version & Save Metadata
        $this->bumpVersionAfterDeploy($branch);

        // Step 5: Clear & Optimize Caches
        $this->clearCaches();

        $duration = round(microtime(true) - $startTime, 2);
        $this->log("✅ Deployment finished successfully in {$duration} seconds!");

        return [
            'success'  => true,
            'message'  => "Updated successfully in {$duration}s",
            'revision' => $this->getCurrentGitRevision(),
            'logs'     => $this->logs,
        ];
    }


    /**
     * Create Pre-Update ZIP Backup
     */
    public function createPreUpdateBackup(): string
    {
        File::ensureDirectoryExists($this->backupDir);
        $timestamp  = now()->format('Ymd_His');
        $backupName = "pre-git-update-{$timestamp}.zip";
        $backupPath = $this->backupDir . '/' . $backupName;

        $zip = new ZipArchive();
        $zip->open($backupPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $dirsToBackup = ['app', 'config', 'routes', 'resources/views', 'public/site.css', 'public/admin.css'];
        foreach ($dirsToBackup as $item) {
            $fullPath = $this->laravelRoot . '/' . $item;
            if (is_file($fullPath)) {
                $zip->addFile($fullPath, $item);
            } elseif (is_dir($fullPath)) {
                $this->addDirToZip($zip, $fullPath, $item);
            }
        }
        $zip->close();

        return $backupPath;
    }

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

    /**
     * Run Git Pull safely
     */
    protected function runGitPull(string $branch): bool
    {
        $this->log("🔄 Pulling latest commits from origin/{$branch}...");

        $cmd = "cd \"{$this->laravelRoot}\" && {$this->gitBinary} fetch origin {$branch} 2>&1 && {$this->gitBinary} reset --hard origin/{$branch} 2>&1";
        
        $output = $this->execCommand($cmd);
        $this->log($output);

        return !str_contains(strtolower($output), 'fatal:') && !str_contains(strtolower($output), 'error:');
    }

    /**
     * If public_html is separated from Laravel root, sync assets
     */
    protected function syncPublicAssets(): void
    {
        $sourcePublic = $this->laravelRoot . DIRECTORY_SEPARATOR . 'public';
        $targetPublic = $this->webRoot;

        if (!is_dir($sourcePublic) || !is_dir($targetPublic)) {
            return;
        }

        $this->log("📂 Syncing public assets to web root ({$targetPublic})...");

        $itemsToSync = ['build', 'css', 'js', 'images', 'vendor', 'site.css', 'admin.css', 'deploy.php', 'robots.txt'];

        foreach ($itemsToSync as $item) {
            $src = $sourcePublic . DIRECTORY_SEPARATOR . $item;
            $dst = $targetPublic . DIRECTORY_SEPARATOR . $item;

            if (is_dir($src)) {
                File::ensureDirectoryExists($dst);
                File::copyDirectory($src, $dst);
                $this->log("  ↳ Copied directory: {$item}");
            } elseif (is_file($src)) {
                File::ensureDirectoryExists(dirname($dst));
                File::copy($src, $dst);
                $this->log("  ↳ Copied file: {$item}");
            }
        }
    }

    /**
     * Run Database Migrations
     */
    protected function runMigrations(): void
    {
        $this->log("🗄️ Running database migrations...");
        try {
            Artisan::call('migrate', ['--force' => true]);
            $this->log(trim(Artisan::output()) ?: '  ↳ No new migrations.');
        } catch (\Throwable $e) {
            $this->log("  ⚠ Migration error: " . $e->getMessage());
        }
    }

    /**
     * Clear and optimize caches
     */
    protected function clearCaches(): void
    {
        $this->log("🧹 Clearing application caches...");
        try {
            Artisan::call('optimize:clear');
            Cache::flush();
            $this->log("  ↳ Config, routes, and views cache cleared successfully.");
        } catch (\Throwable $e) {
            $this->log("  ⚠ Cache clear error: " . $e->getMessage());
        }
    }

    /**
     * Auto-detect Laravel Root
     */
    protected function detectLaravelRoot(): string
    {
        if (function_exists('base_path')) {
            return base_path();
        }

        $candidates = [
            __DIR__ . '/../../..',
            dirname($_SERVER['SCRIPT_FILENAME'] ?? ''),
            dirname($_SERVER['DOCUMENT_ROOT'] ?? '') . '/prayaag',
            dirname($_SERVER['DOCUMENT_ROOT'] ?? '') . '/laravel',
            dirname($_SERVER['DOCUMENT_ROOT'] ?? ''),
            getcwd(),
        ];

        foreach ($candidates as $dir) {
            $real = realpath($dir);
            if ($real && file_exists($real . '/artisan') && file_exists($real . '/bootstrap/app.php')) {
                return $real;
            }
        }

        return getcwd();
    }

    /**
     * Auto-detect Public Web Root (e.g. public_html, htdocs, public)
     */
    protected function detectWebRoot(): ?string
    {
        $candidates = [
            $_SERVER['DOCUMENT_ROOT'] ?? null,
            dirname($this->laravelRoot) . '/public_html',
            dirname($this->laravelRoot) . '/htdocs',
            dirname($this->laravelRoot) . '/www',
            $this->laravelRoot . '/public',
        ];

        foreach ($candidates as $dir) {
            if (!$dir) continue;
            $real = realpath($dir);
            if ($real && is_dir($real) && (file_exists($real . '/index.php') || file_exists($real . '/robots.txt'))) {
                return $real;
            }
        }

        return public_path();
    }

    /**
     * Check if public folder is physically separate from Laravel core's public/
     */
    protected function isPublicSplit(): bool
    {
        if (!$this->webRoot) return false;
        $standardPublic = realpath($this->laravelRoot . DIRECTORY_SEPARATOR . 'public');
        $detectedWeb    = realpath($this->webRoot);
        return $standardPublic !== $detectedWeb;
    }

    /**
     * Auto-detect PHP Binary path
     */
    protected function detectPhpBinary(): string
    {
        if (defined('PHP_BINARY') && file_exists(PHP_BINARY)) {
            return PHP_BINARY;
        }

        $candidates = [
            'C:\\xampp\\php\\php.exe',
            '/usr/local/bin/ea-php83',
            '/usr/local/bin/ea-php82',
            '/usr/bin/php8.3',
            '/usr/bin/php8.2',
            '/usr/bin/php',
            'php',
        ];

        foreach ($candidates as $bin) {
            if (file_exists($bin)) return $bin;
        }

        return 'php';
    }

    /**
     * Auto-detect Git Binary path
     */
    protected function detectGitBinary(): string
    {
        $candidates = [
            '/usr/bin/git',
            '/usr/local/bin/git',
            'C:\\Program Files\\Git\\bin\\git.exe',
            'git',
        ];

        foreach ($candidates as $bin) {
            if (file_exists($bin)) return $bin;
        }

        return 'git';
    }

    /**
     * Get current git commit revision or fallback to config release
     */
    public function getCurrentGitRevision(): string
    {
        $cmd = "cd \"{$this->laravelRoot}\" && {$this->gitBinary} log -1 --pretty=format:\"%h - %s (%ci)\" 2>&1";
        $res = trim($this->execCommand($cmd));

        if (!empty($res) && !str_contains(strtolower($res), 'fatal') && !str_contains(strtolower($res), 'not a git')) {
            return $res;
        }

        $ver   = config('cms.version', '1.3.1');
        $build = config('cms.build', 'efd331d');
        $date  = config('cms.released_at', date('Y-m-d H:i'));

        return "v{$ver} · Build {$build} ({$date})";
    }

    /**
     * Get short commit sha or fallback to config build
     */
    public function getCurrentGitSha(): string
    {
        $cmd = "cd \"{$this->laravelRoot}\" && {$this->gitBinary} rev-parse --short HEAD 2>&1";
        $res = trim($this->execCommand($cmd));

        if (!empty($res) && strlen($res) <= 12 && ctype_alnum($res)) {
            return $res;
        }

        return config('cms.build', 'efd331d');
    }

    /**
     * Bump version number and save to config/cms.php after successful deploy
     */
    public function bumpVersionAfterDeploy(string $branch = 'main'): void
    {
        try {
            $configFile = $this->laravelRoot . '/config/cms.php';
            $currentVer = config('cms.version', '1.3.1');
            $parts      = explode('.', $currentVer);

            if (count($parts) === 3) {
                $parts[2] = ((int) $parts[2]) + 1; // Increment patch version e.g. 1.3.1 -> 1.3.2
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
            $this->log("🔖 Version automatically updated to v{$newVer} (Build: {$newSha})");
        } catch (\Throwable $e) {
            $this->log("  ⚠ Version bump notice: " . $e->getMessage());
        }
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

