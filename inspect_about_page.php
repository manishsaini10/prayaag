<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$page = \App\Models\Page::where('slug', 'about-us')->first();

if ($page) {
    echo "Page: {$page->title} (ID: {$page->id})\n";
    foreach ($page->sections as $secIndex => $sec) {
        echo "Section #{$secIndex} (ID: {$sec->id})\n";
        foreach ($sec->rows as $rIndex => $row) {
            echo "  Row #{$rIndex} (ID: {$row->id})\n";
            foreach ($row->columns as $cIndex => $col) {
                echo "    Column #{$cIndex} (ID: {$col->id})\n";
                foreach ($col->widgets as $wIndex => $w) {
                    echo "      Widget #{$wIndex}: type={$w->widget_type} (ID: {$w->id})\n";
                    print_r($w->settings->pluck('setting_value', 'setting_key')->toArray());
                }
            }
        }
    }
} else {
    echo "About us page not found.\n";
}
