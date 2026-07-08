<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Show latest media records
$latest = DB::table('media')->orderBy('created_at', 'desc')->limit(10)->get(['id', 'original_name', 'path', 'disk', 'created_at']);
echo "Latest media records:\n";
foreach ($latest as $m) {
    echo "  {$m->original_name} -> {$m->path} (disk: {$m->disk}, created: {$m->created_at})\n";
}

// Check if there are 2+ records with Mamta or anju names
$dupes = DB::table('media')
    ->where(function ($q) {
        $q->where('original_name', 'like', '%Mamta%')
          ->orWhere('original_name', 'like', '%anju%')
          ->orWhere('original_name', 'like', '%Prayaag-International-school-panipat-principal%');
    })
    ->orderBy('created_at', 'desc')
    ->get(['id', 'original_name', 'path', 'created_at']);
echo "\nLeadership-related records:\n";
foreach ($dupes as $m) {
    echo "  {$m->original_name} -> {$m->path} ({$m->created_at})\n";
}

// Check the leadership widget's rendered content area for these image URLs
$section = DB::table('page_sections')->where('id', '01ktqwv08324gjkjym166er9dp')->first();
echo "\nHero section?\n";
if ($section) {
    echo "  type={$section->section_type} sort={$section->sort_order}\n";
}

// Find all sections and their types for the home page
$sections = DB::table('page_sections')->where('page_id', '01ktqwsqjbjd6p5mss61wbepj6')->orderBy('sort_order')->get(['id', 'section_type', 'sort_order']);
echo "\nAll sections on home page:\n";
foreach ($sections as $s) {
    echo "  sort={$s->sort_order} type={$s->section_type} id={$s->id}\n";
}
