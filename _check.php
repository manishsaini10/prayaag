<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$pages = DB::table('pages')->get(['id', 'title', 'slug', 'status']);
echo "All pages:\n";
foreach ($pages as $p) {
    echo "  slug={$p->slug} title={$p->title} status={$p->status}\n";
}
