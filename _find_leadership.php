<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$items = DB::table('media')
    ->where(function ($q) {
        $q->where('original_name', 'like', '%Mamta%')
          ->orWhere('original_name', 'like', '%Sachdeva%')
          ->orWhere('original_name', 'like', '%anju%')
          ->orWhere('original_name', 'like', '%gupta%')
          ->orWhere('original_name', 'like', '%principal%')
          ->orWhere('original_name', 'like', '%leadership%');
    })
    ->get(['id', 'original_name', 'path', 'mime_type']);
echo "Found: " . $items->count() . "\n";
foreach ($items as $m) {
    echo "{$m->original_name} -> {$m->path} ({$m->mime_type})\n";
}

// Also search for any file named Mamta_Sachdeva_Principal
$exact = DB::table('media')
    ->where('original_name', 'Mamta_Sachdeva_Principal.webp')
    ->orWhere('original_name', 'Mamta_Sachdeva_Principal.png')
    ->orWhere('original_name', 'Mamta_Sachdeva_Principal.jpg')
    ->get(['id', 'original_name', 'path']);
echo "\nExact match: " . $exact->count() . "\n";
foreach ($exact as $m) {
    echo "{$m->original_name} -> {$m->path}\n";
}

// Check files on disk
$files = glob(__DIR__ . '/storage/app/public/media/imported/*[Mm]amta*');
echo "\nDisk files matching 'mamta': " . count($files) . "\n";
foreach ($files as $f) {
    echo basename($f) . "\n";
}
$files = glob(__DIR__ . '/storage/app/public/media/imported/*[Ss]achdeva*');
echo "\nDisk files matching 'sachdeva': " . count($files) . "\n";
foreach ($files as $f) {
    echo basename($f) . "\n";
}
$files = glob(__DIR__ . '/storage/app/public/media/imported/*[Aa]nju*');
echo "\nDisk files matching 'anju': " . count($files) . "\n";
foreach ($files as $f) {
    echo basename($f) . "\n";
}
