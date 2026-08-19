<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$menus = \App\Models\Mess\MessMenu::with('items')->orderByDesc('effective_from')->get();

echo "Total Menus: " . $menus->count() . "\n";
foreach ($menus as $m) {
    echo "ID: {$m->id} | Title: {$m->title} | From: {$m->effective_from} | To: {$m->effective_to} | Active: " . ($m->is_active ? 'YES' : 'NO') . "\n";
}
