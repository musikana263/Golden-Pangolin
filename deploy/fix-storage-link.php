<?php
// One-shot: create the storage symlink in public_html so uploaded images
// resolve at /storage/<path>. Self-deletes on success.
header('Content-Type: text/plain; charset=utf-8');

$publicHtml = __DIR__;                                            // ~/domains/<domain>/public_html
$appPublic  = __DIR__ . '/../../../goldenpangolin/storage/app/public';
$linkPath   = $publicHtml . '/storage';

echo "Public html : $publicHtml\n";
echo "Source dir  : $appPublic\n";
echo "Link target : $linkPath\n\n";

// Sanity check the storage source.
if (! is_dir($appPublic)) {
    echo "ERROR: source folder doesn't exist. Creating it now...\n";
    @mkdir($appPublic, 0775, true);
    if (! is_dir($appPublic)) {
        echo "FAILED to create $appPublic — check parent permissions.\n";
        exit;
    }
    echo "Created.\n\n";
}

// Resolve real path for the symlink target (absolute path is more reliable
// than a relative one on shared hosting).
$realSource = realpath($appPublic);
echo "Real source : $realSource\n\n";

// Remove anything at the link path first.
if (is_link($linkPath)) {
    echo "Existing symlink found — unlinking.\n";
    @unlink($linkPath);
} elseif (is_dir($linkPath)) {
    echo "WARNING: a real directory exists at $linkPath. Will NOT delete it\n";
    echo "automatically — you may have real files inside. Move them, then\n";
    echo "delete the folder manually, then re-run this script.\n";
    echo "\nDirectory contents:\n";
    foreach (scandir($linkPath) as $f) {
        if ($f === '.' || $f === '..') continue;
        echo "  - $f\n";
    }
    exit;
} elseif (file_exists($linkPath)) {
    echo "A regular file exists at $linkPath — removing.\n";
    @unlink($linkPath);
}

// Try a symlink. If the host disallows symlinks (CageFS / open_basedir can
// sometimes block them), fall back to PHP's file copy mode.
$linked = @symlink($realSource, $linkPath);
if ($linked) {
    echo "OK: symlink created.\n";
    echo "Verify : " . (is_link($linkPath) ? 'is_link=YES' : 'is_link=NO') . "\n";
    echo "Target : " . readlink($linkPath) . "\n";
    @unlink(__FILE__);
    echo "\nDone. This file deleted itself.\n";
    exit;
}

echo "symlink() failed (host may disallow it). Falling back to a directory\n";
echo "and copying files. NOTE: with this fallback, NEW uploads will only\n";
echo "show up after you re-run this script. Symlink is strongly preferred.\n\n";

if (! mkdir($linkPath, 0755)) {
    echo "FAILED to create directory either. Stuck — open a support ticket\n";
    echo "asking your host to enable symlinks for your account, OR ask them\n";
    echo "to add a server-level alias from /storage to $realSource.\n";
    exit;
}

$copied = 0;
$it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($realSource, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);
foreach ($it as $file) {
    $rel = substr($file->getPathname(), strlen($realSource) + 1);
    $dest = $linkPath . '/' . $rel;
    if ($file->isDir()) {
        @mkdir($dest, 0755, true);
    } else {
        @copy($file->getPathname(), $dest);
        $copied++;
    }
}
echo "Copied $copied files. Done (but re-run after each upload — symlinks would be nicer).\n";
