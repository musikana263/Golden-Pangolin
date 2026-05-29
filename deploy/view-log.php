<?php
// Quick Laravel log viewer. Upload to public_html/, visit in browser.
// Shows the last 200 lines so you can see what's actually crashing.
// DELETE THIS FILE once you've diagnosed — it exposes internal errors.

header('Content-Type: text/plain; charset=utf-8');

$root = __DIR__ . '/../../../goldenpangolin';
$log  = $root . '/storage/logs/laravel.log';

if (! file_exists($log)) {
    echo "No log file at $log\n";
    echo "Either nothing has been logged yet, or storage/logs/ wasn't created.\n";
    exit;
}

$size = filesize($log);
echo "Log: $log\n";
echo "Size: " . number_format($size) . " bytes\n";
echo "Modified: " . date('Y-m-d H:i:s', filemtime($log)) . "\n";
echo str_repeat('-', 80) . "\n\n";

// Read last ~200 lines without loading the whole file.
$lines  = 200;
$bytes  = min($size, 64 * 1024);   // last 64 KB
$fp     = fopen($log, 'rb');
fseek($fp, -$bytes, SEEK_END);
$tail   = fread($fp, $bytes);
fclose($fp);

$arr = preg_split('/\R/', $tail);
$arr = array_slice($arr, -$lines);
echo implode("\n", $arr);
