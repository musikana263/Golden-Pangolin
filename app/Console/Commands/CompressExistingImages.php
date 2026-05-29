<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CompressExistingImages extends Command
{
    protected $signature = 'images:compress
                            {--dry-run : Show what would be compressed without writing anything}
                            {--min-kb=200 : Skip files already smaller than this many KB}';

    protected $description = 'Resize/recompress all images in storage/app/public to ~1600px JPEG @ q82. Safe to re-run.';

    private const MAX_DIMENSION = 1600;
    private const JPEG_QUALITY  = 82;

    /** Subfolders of storage/app/public to walk. */
    private const DIRS = ['destinations', 'blogs', 'packages', 'accommodations'];

    public function handle(): int
    {
        if (! extension_loaded('gd')) {
            $this->error('The GD extension is not loaded. Cannot compress.');
            return self::FAILURE;
        }

        $disk    = Storage::disk('public');
        $minBytes = (int) $this->option('min-kb') * 1024;
        $dryRun  = (bool) $this->option('dry-run');

        $totalBefore = 0;
        $totalAfter  = 0;
        $processed   = 0;
        $skipped     = 0;
        $errors      = 0;

        foreach (self::DIRS as $dir) {
            if (! $disk->exists($dir)) {
                $this->line("· {$dir}/ does not exist — skipping");
                continue;
            }

            $files = $disk->files($dir);
            $this->info("Scanning {$dir}/ — " . count($files) . ' files');

            foreach ($files as $relPath) {
                $absPath = $disk->path($relPath);
                $ext     = strtolower(pathinfo($relPath, PATHINFO_EXTENSION));

                if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                    $skipped++;
                    continue;
                }

                $sizeBefore = @filesize($absPath) ?: 0;
                if ($sizeBefore < $minBytes) {
                    $skipped++;
                    continue;
                }

                $compressed = $this->compress($absPath, $ext);
                if ($compressed === null) {
                    $this->warn("  ! could not decode {$relPath}");
                    $errors++;
                    continue;
                }

                $sizeAfter = strlen($compressed);

                // Only overwrite if we actually shrank the file. Avoids making
                // already-optimised images bigger.
                if ($sizeAfter >= $sizeBefore) {
                    $skipped++;
                    continue;
                }

                $totalBefore += $sizeBefore;
                $totalAfter  += $sizeAfter;
                $processed++;

                $pct = round((1 - $sizeAfter / $sizeBefore) * 100);
                $this->line(sprintf(
                    '  %s  %s → %s  (-%d%%)  %s',
                    $dryRun ? '[dry]' : '✓',
                    $this->humanSize($sizeBefore),
                    $this->humanSize($sizeAfter),
                    $pct,
                    $relPath
                ));

                if (! $dryRun) {
                    file_put_contents($absPath, $compressed);
                }
            }
        }

        $this->newLine();
        $this->info("Processed: {$processed}   Skipped: {$skipped}   Errors: {$errors}");
        if ($processed > 0) {
            $saved = $totalBefore - $totalAfter;
            $this->info(sprintf(
                'Saved %s total (%s → %s).%s',
                $this->humanSize($saved),
                $this->humanSize($totalBefore),
                $this->humanSize($totalAfter),
                $dryRun ? '  [dry-run — nothing was written]' : ''
            ));
        }

        return self::SUCCESS;
    }

    private function compress(string $absPath, string $ext): ?string
    {
        $src = match ($ext) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($absPath),
            'png'         => @imagecreatefrompng($absPath),
            'webp'        => @imagecreatefromwebp($absPath),
            default       => null,
        };

        if (! $src) {
            return null;
        }

        if ($ext === 'png') {
            $w = imagesx($src);
            $h = imagesy($src);
            $bg = imagecreatetruecolor($w, $h);
            imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
            imagecopy($bg, $src, 0, 0, 0, 0, $w, $h);
            imagedestroy($src);
            $src = $bg;
        }

        $origW = imagesx($src);
        $origH = imagesy($src);

        [$newW, $newH] = $this->scale($origW, $origH, self::MAX_DIMENSION);
        if ($newW !== $origW || $newH !== $origH) {
            $dst = imagecreatetruecolor($newW, $newH);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
            imagedestroy($src);
            $src = $dst;
        }

        ob_start();
        imagejpeg($src, null, self::JPEG_QUALITY);
        $data = ob_get_clean();
        imagedestroy($src);

        return $data ?: null;
    }

    private function scale(int $w, int $h, int $max): array
    {
        if ($w <= $max && $h <= $max) {
            return [$w, $h];
        }
        if ($w >= $h) {
            return [$max, (int) round($h * $max / $w)];
        }
        return [(int) round($w * $max / $h), $max];
    }

    private function humanSize(int $bytes): string
    {
        if ($bytes >= 1024 * 1024) {
            return round($bytes / 1024 / 1024, 2) . ' MB';
        }
        return round($bytes / 1024) . ' KB';
    }
}
