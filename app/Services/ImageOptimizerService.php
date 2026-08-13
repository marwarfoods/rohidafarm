<?php

namespace App\Services;

class ImageOptimizerService
{
    /**
     * Compress and optimize an image file in-place.
     *
     * @param string $sourcePath The absolute path of the target image file
     * @param int $quality Compression quality (0-100)
     * @return bool True if optimized successfully, false if skipped or failed
     */
    public static function optimize(string $sourcePath, int $quality = 75): bool
    {
        if (!extension_loaded('gd')) {
            // Gracefully skip if GD extension is not enabled (e.g. local CLI environment)
            return false;
        }

        // Get image info
        $info = @getimagesize($sourcePath);
        if (!$info) {
            return false;
        }

        // GD decodes the FULL original resolution into memory before any resizing
        // happens below, so a modestly-sized but high-megapixel JPEG/PNG can blow past
        // PHP's default 128M memory_limit and crash the request with a fatal error.
        // Bump the limit just enough to safely decode this specific image.
        self::ensureSufficientMemory($info[0], $info[1]);

        $mime = $info['mime'];
        $image = null;

        // Load image based on mime type
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = @imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = @imagecreatefrompng($sourcePath);
                break;
            case 'image/webp':
                $image = @imagecreatefromwebp($sourcePath);
                break;
            default:
                return false; // Unsupported format for compression
        }

        if (!$image) {
            return false;
        }

        // Smart auto-resizing constraint for massive high-res photos
        $width = $info[0];
        $height = $info[1];
        $maxDimension = 1600;

        if ($width > $maxDimension || $height > $maxDimension) {
            if ($width > $height) {
                $newWidth = $maxDimension;
                $newHeight = (int) round(($height / $width) * $maxDimension);
            } else {
                $newHeight = $maxDimension;
                $newWidth = (int) round(($width / $height) * $maxDimension);
            }

            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

            if ($mime === 'image/png' || $mime === 'image/webp') {
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);
            }

            imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $resizedImage;
        }

        $success = false;

        // Perform compression and overwrite the original file in-place
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                // imagejpeg quality is 0-100
                $success = @imagejpeg($image, $sourcePath, $quality);
                break;
                
            case 'image/png':
                // Keep transparency settings
                imagealphablending($image, false);
                imagesavealpha($image, true);
                
                // PNG is lossless, so always use maximum compression level (9)
                $success = @imagepng($image, $sourcePath, 9);
                break;
                
            case 'image/webp':
                // imagewebp quality is 0-100
                $success = @imagewebp($image, $sourcePath, $quality);
                break;
        }

        // Free up memory resource
        @imagedestroy($image);

        return $success;
    }

    /**
     * Raise PHP's memory_limit (if needed and possible) so GD can safely decode
     * an image of the given dimensions without a fatal "memory size exhausted" crash.
     * Uses the standard GD estimate: width * height * 4 bytes/pixel * 1.65 overhead factor.
     */
    private static function ensureSufficientMemory(int $width, int $height): void
    {
        $currentLimitBytes = self::iniToBytes((string) ini_get('memory_limit'));

        // -1 means "no limit" — nothing to do.
        if ($currentLimitBytes === -1) {
            return;
        }

        $estimatedDecodeBytes = (int) ceil($width * $height * 4 * 1.65);
        $requiredBytes = memory_get_usage(true) + $estimatedDecodeBytes + (8 * 1024 * 1024); // +8MB safety buffer

        if ($requiredBytes <= $currentLimitBytes) {
            return;
        }

        // Cap the bump at 1GB so a corrupt/malicious file can't exhaust the server.
        $newLimitMb = min((int) ceil($requiredBytes / 1024 / 1024) + 16, 1024);
        @ini_set('memory_limit', $newLimitMb . 'M');
    }

    /**
     * Convert a php.ini-style size value (e.g. "128M", "1G") to bytes.
     */
    private static function iniToBytes(string $value): int
    {
        $value = trim($value);
        if ($value === '' || $value === '-1') {
            return $value === '-1' ? -1 : 0;
        }

        $unit = strtolower(substr($value, -1));
        $number = (int) $value;

        return match ($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => (int) $value,
        };
    }
}
