<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// List all pages
$allPages = DB::table('pages')->get(['id', 'title', 'slug', 'status']);
echo "All pages:\n";
foreach ($allPages as $p) {
    echo "  {$p->slug} ({$p->title}) [{$p->status}]\n";
}

// Check if about or message page exists
$about = DB::table('pages')->where('slug', 'like', '%about%')->orWhere('slug', 'like', '%message%')->orWhere('slug', 'like', '%principal%')->get(['id', 'title', 'slug']);
echo "\nAbout/message pages:\n";
foreach ($about as $p) {
    echo "  {$p->slug} ({$p->title})\n";
}
