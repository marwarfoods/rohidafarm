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
}
