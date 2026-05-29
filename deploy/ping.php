<?php
header('Content-Type: text/plain; charset=utf-8');
echo "PING OK\n";
echo "PHP version : " . PHP_VERSION . "\n";
echo "Script path : " . __FILE__ . "\n";
echo "Working dir : " . __DIR__ . "\n\n";

$root = __DIR__ . '/../../../goldenpangolin';
echo "App root    : $root\n";
echo "App exists  : " . (is_dir($root) ? 'YES' : 'NO') . "\n";
echo ".env exists : " . (file_exists($root . '/.env') ? 'YES' : 'NO') . "\n";
echo "vendor/     : " . (is_dir($root . '/vendor') ? 'YES' : 'NO') . "\n";
echo "storage/    : " . (is_dir($root . '/storage') ? 'YES' : 'NO') . "\n";
echo "logs/       : " . (is_dir($root . '/storage/logs') ? 'YES' : 'NO')
                     . "  writable: " . (is_writable($root . '/storage/logs') ? 'YES' : 'NO') . "\n";
echo "app/public/ : " . (is_dir($root . '/storage/app/public') ? 'YES' : 'NO')
                     . "  writable: " . (is_writable($root . '/storage/app/public') ? 'YES' : 'NO') . "\n";
echo "  storage link in public/  : "
    . (is_link($root . '/public/storage') || is_dir($root . '/public/storage') ? 'YES' : 'NO') . "\n";

echo "\n--- Upload / size limits ---\n";
printf("upload_max_filesize : %s\n", ini_get('upload_max_filesize'));
printf("post_max_size       : %s\n", ini_get('post_max_size'));
printf("memory_limit        : %s\n", ini_get('memory_limit'));
printf("max_execution_time  : %ss\n", ini_get('max_execution_time'));
printf("max_input_time      : %ss\n", ini_get('max_input_time'));
printf("max_file_uploads    : %s\n", ini_get('max_file_uploads'));

echo "\nopen_basedir : " . (ini_get('open_basedir') ?: '(none)') . "\n";

echo "\nbootstrap/cache contents:\n";
foreach (glob($root . '/bootstrap/cache/*') ?: [] as $f) {
    echo "  " . basename($f) . "  (" . filesize($f) . " bytes)\n";
}
