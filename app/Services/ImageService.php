<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    private const DISK = 'public';

    /** Max width/height in pixels — images wider/taller than this are scaled down. */
    private const MAX_DIMENSION = 1600;

    /** JPEG quality (0–100). 82 gives good sharpness at ~3–5× smaller files. */
    private const JPEG_QUALITY = 82;

    public function upload(UploadedFile $file, string $directory): string
    {
        $compressed = $this->compress($file);

        if ($compressed === null) {
            // GD not available or unsupported type — store original
            return $file->store($directory, self::DISK);
        }

        $filename  = $directory . '/' . Str::uuid() . '.jpg';
        Storage::disk(self::DISK)->put($filename, $compressed);

        return $filename;
    }

    public function delete(?string $path): bool
    {
        if ($path && Storage::disk(self::DISK)->exists($path)) {
            return Storage::disk(self::DISK)->delete($path);
        }

        return false;
    }

    public function replace(UploadedFile $file, string $directory, ?string $oldPath): string
    {
        $this->delete($oldPath);

        return $this->upload($file, $directory);
    }

    public function getUrl(?string $path): ?string
    {
        if ($path && Storage::disk(self::DISK)->exists($path)) {
            return Storage::disk(self::DISK)->url($path);
        }

        return null;
    }

    /**
     * Resize to MAX_DIMENSION and re-encode as JPEG at JPEG_QUALITY.
     * Returns the compressed binary string, or null if GD is unavailable
     * or the file is not a supported image type.
     */
    private function compress(UploadedFile $file): ?string
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $mime = $file->getMimeType();
        $path = $file->getRealPath();

        $src = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($path),
            'image/png'               => @imagecreatefrompng($path),
            'image/webp'              => @imagecreatefromwebp($path),
            default                   => null,
        };

        if (! $src) {
            return null;
        }

        // Handle PNG transparency by filling with white before converting to JPEG
        if ($mime === 'image/png') {
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

        [$newW, $newH] = $this->scaleDimensions($origW, $origH, self::MAX_DIMENSION);

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

    private function scaleDimensions(int $w, int $h, int $max): array
    {
        if ($w <= $max && $h <= $max) {
            return [$w, $h];
        }

        if ($w >= $h) {
            return [$max, (int) round($h * $max / $w)];
        }

        return [(int) round($w * $max / $h), $max];
    }
}
