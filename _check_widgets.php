<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Check ALL widget settings for home page
$widgets = DB::table('page_widgets')
    ->join('page_columns', 'page_widgets.column_id', '=', 'page_columns.id')
    ->join('page_rows', 'page_columns.row_id', '=', 'page_rows.id')
    ->join('page_sections', 'page_rows.section_id', '=', 'page_sections.id')
    ->where('page_sections.page_id', '01ktqwsqjbjd6p5mss61wbepj6')
    ->select('page_widgets.id', 'page_widgets.widget_type', 'page_widgets.settings')
    ->get();

echo "All home page widgets:\n";
foreach ($widgets as $w) {
    echo "\n--- {$w->widget_type} (ID: {$w->id}) ---\n";
    $s = json_decode($w->settings ?: '[]', true);
    if (is_array($s) && !empty($s)) {
        foreach ($s as $k => $v) {
            $vstr = is_string($v) ? $v : (is_array($v) ? json_encode($v) : print_r($v, true));
            echo "  {$k}: " . substr($vstr, 0, 300) . "\n";
        }
    } else {
        echo "  (using defaults)\n";
    }
}

// Also check imported pages that might contain these images
echo "\n\nPages that reference these images:\n";
$pages = DB::table('pages')
    ->where('content', 'like', '%Prayaag-International-school-panipat-principal%')
    ->orWhere('content', 'like', '%Mamta_Sachdeva_Principal%')
    ->get(['id', 'title', 'slug']);
echo "Found: " . $pages->count() . "\n";
foreach ($pages as $p) {
    echo "  {$p->title} (slug: {$p->slug})\n";
}

// ALSO check the page builder columns for reference
echo "\n\nSearching page_columns, page_rows, page_sections, page_widgets settings for image refs:\n";
$allWidgets = DB::table('page_widgets')->get(['id', 'widget_type', 'settings']);
foreach ($allWidgets as $w) {
    $s = json_decode($w->settings ?: '[]', true);
    if (is_array($s)) {
        array_walk_recursive($s, function($v, $k) use ($w) {
            if (is_string($v) && (str_contains($v, 'Prayaag-International-school-panipat-principal') || str_contains($v, 'Mamta_Sachdeva_Principal'))) {
                echo "  widget {$w->id} ({$w->widget_type}): {$k} = " . substr($v, 0, 200) . "\n";
            }
        });
    }
}
