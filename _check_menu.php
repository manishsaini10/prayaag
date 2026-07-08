<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$menus = DB::table('menus')->get();
echo "Menus: " . $menus->count() . "\n";
foreach ($menus as $m) {
    echo "  id={$m->id} name={$m->name} slug={$m->slug} location={$m->location}\n";
    $items = DB::table('menu_items')->where('menu_id', $m->id)->get();
    foreach ($items as $item) {
        $pageSlug = '';
        if ($item->page_id) {
            $page = DB::table('pages')->where('id', $item->page_id)->first();
            $pageSlug = $page ? $page->slug : 'MISSING';
        }
        echo "    item: label={$item->label} type={$item->type} page_id={$item->page_id} page_slug={$pageSlug} url={$item->url}\n";
    }
}

echo "\nPublished pages:\n";
$pages = DB::table('pages')->where('status', 'published')->get(['id', 'title', 'slug']);
foreach ($pages as $p) {
    echo "  id={$p->id} title={$p->title} slug={$p->slug}\n";
}
