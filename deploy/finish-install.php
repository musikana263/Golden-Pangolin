<?php
/**
 * Golden Pangolin — finish-install.
 *
 * Run this if install.php self-deleted before migrations completed (the
 * "Table 'sessions' doesn't exist" symptom). This script only runs the
 * remaining steps: migrate, seed, clear & rebuild caches.
 *
 * Upload to:  ~/domains/goldenpangolin.com/public_html/finish-install.php
 * Visit:      https://goldenpangolin.com/finish-install.php
 * It deletes itself on success.
 */

// Same split-folder path as install.php.
$root = __DIR__ . '/../../../goldenpangolin';

header('Content-Type: text/html; charset=utf-8');

$out = '';
$ok  = true;

function say(string $line, bool $good = true): void {
    global $out, $ok;
    $out .= ($good ? '[OK]   ' : '[FAIL] ') . htmlspecialchars($line) . "\n";
    if (!$good) $ok = false;
}

// 1. Sanity check.
if (!file_exists($root . '/vendor/autoload.php')) {
    render('FATAL', "Cannot find vendor/autoload.php at $root/vendor/. Is the app folder in the right place?");
    exit;
}
if (!file_exists($root . '/.env')) {
    render('FATAL', "Cannot find .env at $root/.env.");
    exit;
}

// 2. Make sure runtime directories exist.
foreach ([
    'storage/logs',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/framework/cache/data',
    'storage/app/public',
    'bootstrap/cache',
] as $dir) {
    $full = $root . '/' . $dir;
    if (!is_dir($full)) @mkdir($full, 0775, true);
    @chmod($full, 0775);
}
say("Runtime directories ensured");

// 3. Clear any stale bootstrap cache (e.g. cached empty APP_KEY).
foreach (glob($root . '/bootstrap/cache/*.php') as $f) {
    @unlink($f);
}
say("Cleared bootstrap/cache/*.php");

// 4. Make sure APP_KEY is present in .env BEFORE bootstrapping Laravel.
$env = file_get_contents($root . '/.env');
if (preg_match('/^APP_KEY=\s*$/m', $env) || !preg_match('/^APP_KEY=base64:/m', $env)) {
    $newKey = 'base64:' . base64_encode(random_bytes(32));
    if (preg_match('/^APP_KEY=.*$/m', $env)) {
        $env = preg_replace('/^APP_KEY=.*$/m', "APP_KEY={$newKey}", $env);
    } else {
        $env .= "\nAPP_KEY={$newKey}\n";
    }
    file_put_contents($root . '/.env', $env);
    say("Generated new APP_KEY in .env");
} else {
    say("APP_KEY already set");
}

// 5. Bootstrap Laravel and run remaining commands.
require $root . '/vendor/autoload.php';
$app    = require $root . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$cmdLog = '';
$run = function (string $cmd, array $params = []) use ($kernel, &$cmdLog) {
    $exit  = $kernel->call($cmd, $params);
    $body  = trim($kernel->output());
    $cmdLog .= "\n===== php artisan $cmd =====\n" . $body . "\n";
    if ($exit !== 0) {
        $cmdLog .= "EXIT CODE: $exit\n";
        return false;
    }
    return true;
};

$run('config:clear')   ? say("config:clear")   : say("config:clear FAILED", false);
$run('cache:clear')    ? say("cache:clear")    : say("cache:clear FAILED (often safe to ignore on first run)", true);
$run('migrate', ['--force' => true])
    ? say("migrate --force")
    : say("migrate FAILED — check DB credentials in .env", false);

// Try seed but don't fail the whole script if seeder is missing or non-fatal.
$run('db:seed', ['--force' => true, '--class' => 'Database\\Seeders\\UsersTableSeeder']);
say("db:seed UsersTableSeeder (admin account)");

// storage:link via direct symlink (artisan command sometimes fails on shared hosts).
$publicStorage = $root . '/public/storage';
if (!is_link($publicStorage) && !is_dir($publicStorage)) {
    @symlink($root . '/storage/app/public', $publicStorage)
        ?: @mkdir($publicStorage, 0755, true);
}
say("storage symlink/dir present");

// Production caches.
$run('config:cache') && say("config:cache");
$run('route:cache')  && say("route:cache");
$run('view:cache')   && say("view:cache");

// 6. Self-destruct on success.
if ($ok) @unlink(__FILE__);

render($ok ? 'Install finished' : 'Install completed with errors',
       $ok
         ? "<p>Migrations ran, admin seeded, caches built. This script just deleted itself.</p>"
           . "<p><a class='btn' href='/'>Open the site →</a> &nbsp; <a class='btn' href='/login'>Login →</a></p>"
         : "<p>Some steps failed. Fix the issue, then reload this page (the script did NOT delete itself).</p>",
       $out, $cmdLog);

function render(string $title, string $bodyHtml, string $log = '', string $cmdLog = ''): void {
    ?><!doctype html>
    <html lang="en"><head><meta charset="utf-8">
    <title>Golden Pangolin — Finish Install</title>
    <style>
        body { font-family:-apple-system,'Segoe UI',sans-serif; background:#1a1208; color:#fbf3e1;
               margin:0; padding:40px 16px; min-height:100vh; }
        .card { max-width:760px; margin:0 auto; background:#2a1c10;
                border:1px solid rgba(212,160,60,.35); border-radius:14px; padding:32px 28px; }
        h1 { margin:0 0 12px; font-size:1.5rem; color:#f3c779; }
        pre { background:#0c0c10; padding:14px; border-radius:8px; max-height:340px;
              overflow:auto; font-size:.78rem; line-height:1.5; color:#fbf3e1; white-space:pre-wrap; }
        .btn { display:inline-block; margin-top:10px; background:linear-gradient(135deg,#d4a03c,#b8872e);
               color:#1a1208 !important; padding:10px 18px; border-radius:10px; font-weight:700;
               text-decoration:none; margin-right:6px; }
        details summary { cursor:pointer; color:#f3c779; margin-top:10px; }
    </style></head><body>
    <div class="card">
        <h1><?= htmlspecialchars($title) ?></h1>
        <?= $bodyHtml ?>
        <?php if ($log): ?>
            <h3 style="margin-top:24px;color:#f3c779;font-size:1rem;">Step log</h3>
            <pre><?= htmlspecialchars($log) ?></pre>
        <?php endif; ?>
        <?php if ($cmdLog): ?>
            <details><summary>Artisan output</summary>
            <pre><?= htmlspecialchars($cmdLog) ?></pre></details>
        <?php endif; ?>
    </div>
    </body></html><?php
}
