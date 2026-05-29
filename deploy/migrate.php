<?php
// Minimal migration runner. Errors will be displayed inline.
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
header('Content-Type: text/plain; charset=utf-8');

$root = __DIR__ . '/../../../goldenpangolin';

echo "[1] App root: $root\n";

// Clear any stale bootstrap cache first.
foreach (glob($root . '/bootstrap/cache/*.php') ?: [] as $f) {
    @unlink($f);
    echo "    cleared " . basename($f) . "\n";
}

echo "[2] Loading Laravel...\n";
require $root . '/vendor/autoload.php';
$app    = require $root . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "[3] Running migrate --force...\n";
$exit = $kernel->call('migrate', ['--force' => true]);
echo $kernel->output();
echo "    exit code: $exit\n\n";

echo "[4] Running db:seed --force --class=UsersTableSeeder...\n";
$exit = $kernel->call('db:seed', ['--force' => true, '--class' => 'Database\\Seeders\\UsersTableSeeder']);
echo $kernel->output();
echo "    exit code: $exit\n\n";

echo "[5] Rebuilding config + route caches...\n";
$kernel->call('config:cache');
echo $kernel->output();
$kernel->call('route:cache');
echo $kernel->output();

echo "\nDONE. Visit https://goldenpangolin.com/ to test, then delete migrate.php and ping.php.\n";
