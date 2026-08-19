<?php
/**
 * Standalone Zero-Touch Git Deployer for Shared Hosting & cPanel
 * 
 * Usage:
 * https://yourdomain.com/deploy.php?token=YOUR_APP_KEY_HASH
 */

// 1. Security Token Verification
$envFile = __DIR__ . '/../.env';
if (!file_exists($envFile)) {
    $envFile = __DIR__ . '/.env';
}

$appKey = '';
if (file_exists($envFile)) {
    $lines = file($envFile);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), 'APP_KEY=')) {
            $appKey = trim(str_replace('APP_KEY=', '', $line));
            break;
        }
    }
}

$expectedToken = substr(hash('sha256', $appKey . 'deploy_secret'), 0, 32);
$token = $_GET['token'] ?? $_POST['token'] ?? '';

if (empty($token) || !hash_equals($expectedToken, $token)) {
    http_response_code(403);
    die(json_encode(['error' => 'Unauthorized: Invalid deploy token']));
}

// 2. Auto-Detect Laravel Root
$laravelRoot = realpath(__DIR__ . '/..');
if (!file_exists($laravelRoot . '/artisan')) {
    $laravelRoot = realpath(__DIR__);
}

// 3. Auto-Detect Public Web Root
$webRoot = $_SERVER['DOCUMENT_ROOT'] ?? __DIR__;

// 4. Locate PHP & Git
$phpBin = 'php';
$gitBin = 'git';

$possiblePhp = ['/usr/local/bin/ea-php83', '/usr/local/bin/ea-php82', '/usr/bin/php8.3', '/usr/bin/php8.2', '/usr/bin/php', 'php'];
foreach ($possiblePhp as $p) {
    if (@file_exists($p)) { $phpBin = $p; break; }
}

$possibleGit = ['/usr/bin/git', '/usr/local/bin/git', 'git'];
foreach ($possibleGit as $g) {
    if (@file_exists($g)) { $gitBin = $g; break; }
}

// 5. Execute Pipeline
header('Content-Type: application/json');
$logs = [];
$logs[] = "🚀 Starting Auto-Deploy on " . date('Y-m-d H:i:s');
$logs[] = "📍 Laravel Root: " . $laravelRoot;
$logs[] = "🌐 Web Root: " . $webRoot;

// Git Pull
$gitCmd = "cd \"{$laravelRoot}\" && {$gitBin} fetch origin main 2>&1 && {$gitBin} reset --hard origin/main 2>&1";
$gitOut = trim((string)shell_exec($gitCmd));
$logs[] = "🔄 Git Output:\n" . $gitOut;

// Asset Sync if Split
$sourcePublic = realpath($laravelRoot . '/public');
$targetPublic = realpath($webRoot);
if ($sourcePublic && $targetPublic && $sourcePublic !== $targetPublic) {
    $logs[] = "📂 Syncing assets to " . $targetPublic;
    $syncCmd = "cp -r \"{$sourcePublic}\"/* \"{$targetPublic}\"/ 2>&1";
    shell_exec($syncCmd);
}

// Migrations
$migCmd = "cd \"{$laravelRoot}\" && {$phpBin} artisan migrate --force 2>&1";
$migOut = trim((string)shell_exec($migCmd));
$logs[] = "🗄️ Migrations:\n" . $migOut;

// Cache Clear
$cacheCmd = "cd \"{$laravelRoot}\" && {$phpBin} artisan optimize:clear 2>&1";
$cacheOut = trim((string)shell_exec($cacheCmd));
$logs[] = "🧹 Cache:\n" . $cacheOut;

$logs[] = "✅ Auto-Deploy completed successfully!";

echo json_encode([
    'status' => 'success',
    'logs'   => $logs,
], JSON_PRETTY_PRINT);
