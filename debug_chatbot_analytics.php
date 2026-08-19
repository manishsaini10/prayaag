<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::first();
auth()->login($user);

$req = Illuminate\Http\Request::create('/admin/chatbot/analytics', 'GET');
$res = $app->handle($req);

echo "STATUS: " . $res->getStatusCode() . "\n";
if ($res->getStatusCode() !== 200) {
    echo "CONTENT:\n" . substr(strip_tags($res->getContent()), 0, 1000) . "\n";
}
