<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Find the leadership widget
$widget = DB::table('page_widgets')->where('widget_type', 'leadership')->first();
if (!$widget) { echo "No leadership widget found\n"; exit; }

echo "Widget id: {$widget->id}\n";
echo "Settings:\n";
$settings = json_decode($widget->settings ?: '[]', true);
print_r($settings);

// Also check page_widget_settings
$ws = DB::table('page_widget_settings')->where('widget_id', $widget->id)->get();
echo "\npage_widget_settings (" . $ws->count() . "):\n";
foreach ($ws as $s) {
    echo "  {$s->key} = " . substr($s->value ?? '', 0, 200) . "\n";
}
