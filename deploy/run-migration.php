<?php
// One-shot migration runner. Upload to public_html/, visit in browser,
// it runs `php artisan migrate --force` then self-deletes.
ini_set('display_errors', '1');
error_reporting(E_ALL);
header('Content-Type: text/plain; charset=utf-8');

$root = __DIR__ . '/../../../goldenpangolin';

// Clear stale bootstrap cache so any .env changes / new config keys load fresh.
foreach (glob($root . '/bootstrap/cache/*.php') ?: [] as $f) {
    @unlink($f);
}

require $root . '/vendor/autoload.php';
$app    = require $root . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "Running php artisan migrate --force...\n\n";
$exit = $kernel->call('migrate', ['--force' => true]);
echo $kernel->output();
echo "\nexit code: $exit\n";

if ($exit === 0) {
    @unlink(__FILE__);
    echo "\nDone. This file just deleted itself.\n";
} else {
    echo "\nMigration failed — file NOT deleted so you can retry.\n";
}
