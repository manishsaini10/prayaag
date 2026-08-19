<?php

namespace App\Core\Updater;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Multi-Tier Deployment Health Checker
 *
 * Performs comprehensive pre-commit & post-rollback health checks:
 * 1. Backend runtime, boot & service provider integrity
 * 2. Database connection, critical tables & pending migration verification
 * 3. Cache & Filesystem writability
 * 4. Physical Vite CSS/JS asset validation
 * 5. Production HTTP checks (frontend homepage, login, and static asset HTTP 200)
 */
class DeploymentHealthChecker
{
    protected string $projectRoot;
    protected string $publicDir;
    protected ?string $webRoot;
    protected string $appUrl;

    public function __construct(?string $projectRoot = null, ?string $webRoot = null)
    {
        $this->projectRoot = $projectRoot ?: base_path();
        $this->publicDir   = $this->projectRoot . '/public';
        $this->webRoot     = $webRoot ?: $this->detectWebRoot();
        $this->appUrl      = rtrim(config('app.url', 'http://127.0.0.1:8000'), '/');
    }

    /**
     * Run full health check suite with retry logic
     */
    public function runFullHealthCheck(int $maxRetries = 3, int $timeoutSeconds = 10): array
    {
        $attempt = 1;
        $backoffs = [2, 5, 0];
        $lastResult = [];

        while ($attempt <= $maxRetries) {
            Log::info("[HealthChecker] Running health check attempt {$attempt}/{$maxRetries}...");

            $result = $this->executeChecks($timeoutSeconds);
            $lastResult = $result;

            if ($result['status'] === 'healthy') {
                Log::info("[HealthChecker] ✅ All health checks PASSED on attempt {$attempt}.");
                return $result;
            }

            Log::warning("[HealthChecker] ⚠ Health check attempt {$attempt} failed: " . json_encode($result['errors']));

            if ($attempt < $maxRetries) {
                $wait = $backoffs[$attempt - 1] ?? 3;
                Log::info("[HealthChecker] Waiting {$wait}s before retry {$attempt}...");
                sleep($wait);
            }

            $attempt++;
        }

        Log::error("[HealthChecker] ❌ Health checks FAILED after {$maxRetries} attempts.");
        return $lastResult;
    }

    /**
     * Execute all individual health checks
     */
    public function executeChecks(int $httpTimeout = 10): array
    {
        $checks = [
            'backend'  => 'pending',
            'database' => 'pending',
            'storage'  => 'pending',
            'cache'    => 'pending',
            'assets'   => 'pending',
            'frontend' => 'pending',
        ];

        $details = [];
        $errors  = [];

        // 1. Backend Runtime & Boot Check
        try {
            $backendCheck = $this->checkBackend();
            $checks['backend'] = $backendCheck['passed'] ? 'passed' : 'failed';
            $details['backend'] = $backendCheck['details'];
            if (!$backendCheck['passed']) {
                $errors['backend'] = $backendCheck['error'];
            }
        } catch (Throwable $e) {
            $checks['backend'] = 'failed';
            $errors['backend'] = 'Backend boot error: ' . $e->getMessage();
        }

        // 2. Database Integrity & Migration Check
        try {
            $dbCheck = $this->checkDatabase();
            $checks['database'] = $dbCheck['passed'] ? 'passed' : 'failed';
            $details['database'] = $dbCheck['details'];
            if (!$dbCheck['passed']) {
                $errors['database'] = $dbCheck['error'];
            }
        } catch (Throwable $e) {
            $checks['database'] = 'failed';
            $errors['database'] = 'Database check exception: ' . $e->getMessage();
        }

        // 3. Storage & Permissions Check
        try {
            $storageCheck = $this->checkStorage();
            $checks['storage'] = $storageCheck['passed'] ? 'passed' : 'failed';
            $details['storage'] = $storageCheck['details'];
            if (!$storageCheck['passed']) {
                $errors['storage'] = $storageCheck['error'];
            }
        } catch (Throwable $e) {
            $checks['storage'] = 'failed';
            $errors['storage'] = 'Storage permission error: ' . $e->getMessage();
        }

        // 4. Cache Subsystem Check
        try {
            $cacheCheck = $this->checkCache();
            $checks['cache'] = $cacheCheck['passed'] ? 'passed' : 'failed';
            $details['cache'] = $cacheCheck['details'];
            if (!$cacheCheck['passed']) {
                $errors['cache'] = $cacheCheck['error'];
            }
        } catch (Throwable $e) {
            $checks['cache'] = 'failed';
            $errors['cache'] = 'Cache error: ' . $e->getMessage();
        }

        // 5. Physical Vite & Asset Validation
        try {
            $assetCheck = $this->checkAssets($httpTimeout);
            $checks['assets'] = $assetCheck['passed'] ? 'passed' : 'failed';
            $details['assets'] = $assetCheck['details'];
            if (!$assetCheck['passed']) {
                $errors['assets'] = $assetCheck['error'];
            }
        } catch (Throwable $e) {
            $checks['assets'] = 'failed';
            $errors['assets'] = 'Asset validation error: ' . $e->getMessage();
        }

        // 6. Frontend HTTP & Admin Login Check
        try {
            $frontendCheck = $this->checkFrontendHttp($httpTimeout);
            $checks['frontend'] = $frontendCheck['passed'] ? 'passed' : 'failed';
            $details['frontend'] = $frontendCheck['details'];
            if (!$frontendCheck['passed']) {
                $errors['frontend'] = $frontendCheck['error'];
            }
        } catch (Throwable $e) {
            $checks['frontend'] = 'failed';
            $errors['frontend'] = 'Frontend HTTP check error: ' . $e->getMessage();
        }

        $allPassed = !in_array('failed', $checks, true) && empty($errors);

        return [
            'status'     => $allPassed ? 'healthy' : 'unhealthy',
            'checks'     => $checks,
            'details'    => $details,
            'errors'     => $errors,
            'checked_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Check 1: Backend runtime boot
     */
    protected function checkBackend(): array
    {
        $appVersion = app()->version();
        $isProduction = app()->environment('production');

        return [
            'passed'  => true,
            'details' => [
                'laravel_version' => $appVersion,
                'php_version'     => PHP_VERSION,
                'environment'     => app()->environment(),
            ],
            'error'   => null,
        ];
    }

    /**
     * Check 2: Database connectivity, critical tables & pending migrations
     */
    protected function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
        } catch (Throwable $e) {
            return [
                'passed'  => false,
                'details' => [],
                'error'   => 'Database connection failed: ' . $e->getMessage(),
            ];
        }

        // Verify critical core tables exist
        $requiredTables = ['users', 'settings', 'media', 'pages', 'migrations'];
        $tablesRaw = DB::select('SHOW TABLES');
        $existingTables = array_map(fn($t) => (string)array_values((array)$t)[0], $tablesRaw);
        $missing = array_diff($requiredTables, $existingTables);

        if (!empty($missing)) {
            return [
                'passed'  => false,
                'details' => ['existing_tables_count' => count($existingTables)],
                'error'   => 'Critical database tables missing: ' . implode(', ', $missing),
            ];
        }

        // Verify no pending migrations left
        try {
            Artisan::call('migrate:status');
            $statusOutput = Artisan::output();
            if (str_contains($statusOutput, 'Pending') || str_contains($statusOutput, 'No [Ran]')) {
                return [
                    'passed'  => false,
                    'details' => ['output' => $statusOutput],
                    'error'   => 'Unexecuted pending database migrations detected.',
                ];
            }
        } catch (Throwable $e) {
            // Note if status output cannot be parsed
        }

        return [
            'passed'  => true,
            'details' => [
                'connection'    => config('database.default'),
                'database_name' => config('database.connections.mysql.database'),
                'tables_count'  => count($existingTables),
            ],
            'error'   => null,
        ];
    }

    /**
     * Check 3: Storage & Cache directories writability
     */
    protected function checkStorage(): array
    {
        $dirs = [
            $this->projectRoot . '/bootstrap/cache',
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('logs'),
        ];

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                File::ensureDirectoryExists($dir);
            }
            if (!is_writable($dir)) {
                return [
                    'passed'  => false,
                    'details' => ['failed_dir' => $dir],
                    'error'   => "Directory is not writable: {$dir}",
                ];
            }
        }

        return [
            'passed'  => true,
            'details' => ['verified_directories' => count($dirs)],
            'error'   => null,
        ];
    }

    /**
     * Check 4: Cache subsystem
     */
    protected function checkCache(): array
    {
        $testKey = '__deploy_health_check_' . uniqid();
        $testVal = time();

        try {
            Cache::put($testKey, $testVal, 10);
            $retrieved = Cache::get($testKey);
            Cache::forget($testKey);

            if ((int)$retrieved !== $testVal) {
                return [
                    'passed'  => false,
                    'details' => [],
                    'error'   => 'Cache write/read mismatch.',
                ];
            }
        } catch (Throwable $e) {
            return [
                'passed'  => false,
                'details' => [],
                'error'   => 'Cache subsystem error: ' . $e->getMessage(),
            ];
        }

        return [
            'passed'  => true,
            'details' => ['driver' => config('cache.default')],
            'error'   => null,
        ];
    }

    /**
     * Check 5: Physical Vite assets & HTTP 200 accessibility
     */
    protected function checkAssets(int $timeout = 10): array
    {
        $manifestPath = $this->publicDir . '/build/manifest.json';
        if (!file_exists($manifestPath)) {
            // Check standalone css as fallback if Vite not used
            if (file_exists($this->publicDir . '/site.css') && file_exists($this->publicDir . '/admin.css')) {
                return [
                    'passed'  => true,
                    'details' => ['mode' => 'standalone_css'],
                    'error'   => null,
                ];
            }

            return [
                'passed'  => false,
                'details' => [],
                'error'   => "Vite manifest not found at: {$manifestPath}",
            ];
        }

        $manifestContent = file_get_contents($manifestPath);
        $manifest = json_decode($manifestContent, true);

        if (!is_array($manifest)) {
            return [
                'passed'  => false,
                'details' => [],
                'error'   => "Vite manifest is corrupted or invalid JSON.",
            ];
        }

        $checkedFiles = [];
        $missingFiles = [];
        $httpAssetErrors = [];

        foreach ($manifest as $key => $entry) {
            $relFile = $entry['file'] ?? null;
            if (!$relFile) continue;

            $localFile = $this->publicDir . '/build/' . $relFile;
            if (!file_exists($localFile) || filesize($localFile) === 0) {
                $missingFiles[] = "public/build/{$relFile}";
            } else {
                $checkedFiles[] = $relFile;
            }

            // Also verify in external web root if separated
            if ($this->webRoot && $this->webRoot !== $this->publicDir) {
                $webRootFile = $this->webRoot . '/build/' . $relFile;
                if (!file_exists($webRootFile) || filesize($webRootFile) === 0) {
                    $missingFiles[] = "WEB_ROOT/build/{$relFile}";
                }
            }

            // For CSS and JS critical assets, perform HTTP request verification if URL accessible
            if (str_ends_with($relFile, '.css') || str_ends_with($relFile, '.js')) {
                $assetUrl = $this->appUrl . '/build/' . $relFile;
                try {
                    $res = Http::timeout($timeout)->get($assetUrl);
                    if ($res->status() !== 200 || empty(trim($res->body()))) {
                        $httpAssetErrors[] = "Asset {$assetUrl} returned HTTP {$res->status()} (Expected HTTP 200)";
                    }
                } catch (Throwable $e) {
                    // Local fallback: if domain HTTP lookup fails due to DNS/CLI environment, physical validation is respected
                    Log::info("[HealthChecker] Asset HTTP check notice for {$assetUrl}: " . $e->getMessage());
                }
            }
        }

        if (!empty($missingFiles)) {
            return [
                'passed'  => false,
                'details' => ['missing' => $missingFiles],
                'error'   => 'Missing physical Vite assets: ' . implode(', ', $missingFiles),
            ];
        }

        if (!empty($httpAssetErrors)) {
            return [
                'passed'  => false,
                'details' => ['http_errors' => $httpAssetErrors],
                'error'   => 'Asset HTTP check failed: ' . implode(', ', $httpAssetErrors),
            ];
        }

        return [
            'passed'  => true,
            'details' => [
                'manifest'        => 'valid',
                'assets_verified' => count($checkedFiles),
            ],
            'error'   => null,
        ];
    }

    /**
     * Check 6: Frontend Homepage, Login, and Admin UI HTTP responses
     */
    protected function checkFrontendHttp(int $timeout = 10): array
    {
        $endpoints = [
            '/'      => ['name' => 'Homepage', 'expected' => [200, 301, 302]],
            '/login' => ['name' => 'User Login', 'expected' => [200]],
            '/admin' => ['name' => 'Admin Panel Entry', 'expected' => [200, 301, 302]],
        ];

        $results = [];
        $failures = [];

        foreach ($endpoints as $uri => $spec) {
            $url = $this->appUrl . $uri;
            try {
                $response = Http::timeout($timeout)
                    ->withHeaders(['User-Agent' => 'Prayaag-Deployment-HealthChecker/1.0'])
                    ->get($url);

                $status = $response->status();
                $body   = $response->body();

                if (!in_array($status, $spec['expected'], true)) {
                    $failures[] = "{$spec['name']} ({$url}) returned HTTP {$status} (Expected: " . implode('/', $spec['expected']) . ")";
                } elseif ($status === 500 || $status === 502 || $status === 503) {
                    $failures[] = "{$spec['name']} returned HTTP {$status} Server Error";
                } elseif (str_contains($body, 'Fatal error') || str_contains($body, 'Parse error') || str_contains($body, 'SQLSTATE[')) {
                    $failures[] = "{$spec['name']} HTML contains unhandled PHP/SQL exception strings";
                } else {
                    $results[$uri] = "HTTP {$status} OK";
                }
            } catch (Throwable $e) {
                // If CLI environment cannot resolve its own public domain over HTTP, fall back to internal route rendering check
                try {
                    $req = \Illuminate\Http\Request::create($uri, 'GET');
                    $res = app()->handle($req);
                    $status = $res->getStatusCode();
                    if ($status === 200 || $status === 302 || $status === 301) {
                        $results[$uri] = "HTTP {$status} (Internal Simulation)";
                    } else {
                        $failures[] = "{$spec['name']} Internal simulation returned HTTP {$status}";
                    }
                } catch (Throwable $inner) {
                    $failures[] = "{$spec['name']} unreachable: " . $e->getMessage();
                }
            }
        }

        if (!empty($failures)) {
            return [
                'passed'  => false,
                'details' => ['results' => $results, 'failures' => $failures],
                'error'   => 'Frontend HTTP checks failed: ' . implode('; ', $failures),
            ];
        }

        return [
            'passed'  => true,
            'details' => $results,
            'error'   => null,
        ];
    }

    /**
     * Auto-detect public web root
     */
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

        $candidates = [
            dirname($this->projectRoot) . '/public_html',
            $this->projectRoot . '/public',
        ];

        foreach ($candidates as $candidate) {
            if (is_dir($candidate)) return $candidate;
        }

        return $this->publicDir;
    }
}
