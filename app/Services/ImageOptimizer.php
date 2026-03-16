<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageOptimizer
{
    /**
     * Optimize an image stored in the given disk.
     * Reduces quality to 80% for JPEG and resizes if wider than maxWidth.
     *
     * @param string $path    Storage path (e.g. 'projects/abc.jpg')
     * @param string $disk    Storage disk (default: 'public')
     * @param int    $quality JPEG/PNG compression quality (1–100)
     * @param int    $maxWidth Maximum width in pixels
     */
    public static function optimize(string $path, string $disk = 'public', int $quality = 80, int $maxWidth = 1920): bool
    {
        try {
            if (!Storage::disk($disk)->exists($path)) {
                return false;
            }

            $fullPath = Storage::disk($disk)->path($path);
            $imageInfo = @getimagesize($fullPath);

            if (!$imageInfo) {
                return false;
            }

            [$width, $height, $type] = $imageInfo;

            $image = match ($type) {
                IMAGETYPE_JPEG => @imagecreatefromjpeg($fullPath),
                IMAGETYPE_PNG  => @imagecreatefrompng($fullPath),
                IMAGETYPE_WEBP => @imagecreatefromwebp($fullPath),
                default        => null,
            };

            if (!$image) {
                return false;
            }

            // Resize if wider than maxWidth
            if ($width > $maxWidth) {
                $ratio  = $maxWidth / $width;
                $newH   = (int) ($height * $ratio);
                $resized = imagecreatetruecolor($maxWidth, $newH);

                // Preserve transparency for PNG
                if ($type === IMAGETYPE_PNG) {
                    imagealphablend($resized, false);
                    imagesavealpha($resized, true);
                    $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
                    imagefilledrectangle($resized, 0, 0, $maxWidth, $newH, $transparent);
                }

                imagecopyresampled($resized, $image, 0, 0, 0, 0, $maxWidth, $newH, $width, $height);
                imagedestroy($image);
                $image = $resized;
            }

            // Save back
            $saved = match ($type) {
                IMAGETYPE_JPEG => imagejpeg($image, $fullPath, $quality),
                IMAGETYPE_PNG  => imagepng($image, $fullPath, (int) round((100 - $quality) / 10)),
                IMAGETYPE_WEBP => imagewebp($image, $fullPath, $quality),
                default        => false,
            };

            imagedestroy($image);
            return $saved;
        } catch (\Throwable $e) {
            Log::warning("ImageOptimizer failed for {$path}: " . $e->getMessage());
            return false;
        }
    }
}
