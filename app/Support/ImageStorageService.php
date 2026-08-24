<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImageStorageService
{
    /** Maximum width/height for stored originals. Images larger than this are resized. */
    private const MAX_ORIGINAL_DIMENSION = 2000;

    /** JPEG quality for stored originals (0-100). */
    private const ORIGINAL_JPEG_QUALITY = 82;

    /** Store a public file and return its browser path. */
    public function storePublicFile(UploadedFile $file, string $directory): string
    {
        $directory = trim($directory, '/');
        [$filename] = $this->managedFilename($file);

        MediaStorage::disk()->putFileAs($directory, $file, $filename);

        return 'storage/' . $directory . '/' . $filename;
    }

    /**
     * Store a public image together with a generated thumbnail.
     *
     * @return array{path: string, thumb_path: string}
     */
    public function storePublicImageWithThumbnail(
        UploadedFile $file,
        string $directory,
        string $thumbDirectory,
        int $thumbWidth,
        int $thumbHeight,
    ): array {
        $directory = trim($directory, '/');
        $thumbDirectory = trim($thumbDirectory, '/');
        [$filename] = $this->managedFilename($file);

        // 1) Store the original (resized down when it exceeds the dimension limit)
        $this->storeOriginal($file, $directory, $filename);
        $path = 'storage/' . $directory . '/' . $filename;

        // 2) Generate a thumbnail; fall back to the original on failure
        $thumbPath = $this->createThumbnail(
            $file,
            $thumbDirectory,
            'thumb_' . $filename,
            $thumbWidth,
            $thumbHeight,
        ) ?? $path;

        return [
            'path' => $path,
            'thumb_path' => $thumbPath,
        ];
    }

    /**
     * Build a collision-free managed filename from an upload.
     *
     * @return array{0: string, 1: string} [extension, filename]
     */
    private function managedFilename(UploadedFile $file): array
    {
        $extension = $this->resolveOriginalExtension($file);

        return [$extension, (string) Str::uuid() . '.' . $extension];
    }

    /**
     * Store the original upload on the media disk, resizing it first when needed.
     */
    private function storeOriginal(UploadedFile $file, string $directory, string $filename): void
    {
        $sourcePath = $file->getRealPath();

        if (! is_string($sourcePath) || $sourcePath === '') {
            MediaStorage::disk()->putFileAs($directory, $file, $filename);

            return;
        }

        $imageInfo = @getimagesize($sourcePath);

        if ($imageInfo === false) {
            MediaStorage::disk()->putFileAs($directory, $file, $filename);

            return;
        }

        $width = (int) ($imageInfo[0] ?? 0);
        $height = (int) ($imageInfo[1] ?? 0);
        $mimeType = $imageInfo['mime'] ?? null;

        if ($width <= self::MAX_ORIGINAL_DIMENSION && $height <= self::MAX_ORIGINAL_DIMENSION) {
            MediaStorage::disk()->putFileAs($directory, $file, $filename);

            return;
        }

        $sourceImage = $this->createImageResource($sourcePath, $mimeType);

        if ($sourceImage === null) {
            MediaStorage::disk()->putFileAs($directory, $file, $filename);

            return;
        }

        $scale = self::MAX_ORIGINAL_DIMENSION / max($width, $height);
        $newWidth = max((int) round($width * $scale), 1);
        $newHeight = max((int) round($height * $scale), 1);

        $resized = imagecreatetruecolor($newWidth, $newHeight);

        if ($resized !== false) {
            $this->prepareTargetCanvas($resized, $mimeType);
            imagecopyresampled($resized, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        }

        imagedestroy($sourceImage);

        if ($resized === false) {
            MediaStorage::disk()->putFileAs($directory, $file, $filename);

            return;
        }

        $bytes = $this->imageToString($resized, $mimeType, self::ORIGINAL_JPEG_QUALITY);
        imagedestroy($resized);

        if ($bytes === null || ! MediaStorage::disk()->put($directory . '/' . $filename, $bytes)) {
            Log::warning('Resized original could not be stored; keeping raw upload instead.', [
                'key' => $directory . '/' . $filename,
            ]);
            MediaStorage::disk()->putFileAs($directory, $file, $filename);
        }
    }

    /**
     * Generate a thumbnail and store it on the media disk.
     */
    private function createThumbnail(
        UploadedFile $file,
        string $directory,
        string $filename,
        int $targetWidth,
        int $targetHeight,
    ): ?string {
        if (! extension_loaded('gd')) {
            return null;
        }

        $sourcePath = $file->getRealPath();

        if (! is_string($sourcePath) || $sourcePath === '') {
            return null;
        }

        $imageInfo = @getimagesize($sourcePath);

        if ($imageInfo === false) {
            return null;
        }

        $mimeType = $imageInfo['mime'] ?? null;
        $sourceImage = $this->createImageResource($sourcePath, $mimeType);

        if ($sourceImage === null) {
            return null;
        }

        $sourceWidth = max((int) ($imageInfo[0] ?? 0), 1);
        $sourceHeight = max((int) ($imageInfo[1] ?? 0), 1);
        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        if ($targetImage === false) {
            imagedestroy($sourceImage);

            return null;
        }

        $this->prepareTargetCanvas($targetImage, $mimeType);

        $sourceRatio = $sourceWidth / $sourceHeight;
        $targetRatio = $targetWidth / $targetHeight;

        if ($sourceRatio > $targetRatio) {
            $cropHeight = $sourceHeight;
            $cropWidth = (int) round($sourceHeight * $targetRatio);
            $sourceX = (int) floor(($sourceWidth - $cropWidth) / 2);
            $sourceY = 0;
        } else {
            $cropWidth = $sourceWidth;
            $cropHeight = (int) round($sourceWidth / $targetRatio);
            $sourceX = 0;
            $sourceY = (int) floor(($sourceHeight - $cropHeight) / 2);
        }

        imagecopyresampled(
            $targetImage,
            $sourceImage,
            0,
            0,
            $sourceX,
            $sourceY,
            $targetWidth,
            $targetHeight,
            $cropWidth,
            $cropHeight,
        );

        imagedestroy($sourceImage);

        $bytes = $this->imageToString($targetImage, $mimeType);
        imagedestroy($targetImage);

        $key = trim($directory, '/') . '/' . $filename;

        if ($bytes === null || ! MediaStorage::disk()->put($key, $bytes)) {
            Log::warning('Thumbnail generation failed for uploaded image.', [
                'key' => $key,
                'mime' => $mimeType,
            ]);

            return null;
        }

        return 'storage/' . $key;
    }

    /**
     * Resolve the extension for an uploaded file.
     */
    private function resolveOriginalExtension(UploadedFile $file): string
    {
        $extension = strtolower((string) ($file->guessExtension() ?: $file->extension() ?: 'jpg'));

        return match ($extension) {
            'jpeg', 'jpg', 'png', 'gif', 'webp' => $extension,
            default => 'jpg',
        };
    }

    /**
     * Create an image resource from a supported mime type.
     */
    private function createImageResource(string $path, ?string $mimeType)
    {
        return match ($mimeType) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/gif' => @imagecreatefromgif($path),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : null,
            default => null,
        };
    }

    /**
     * Prepare the destination canvas with the correct background.
     */
    private function prepareTargetCanvas(\GdImage $targetImage, ?string $mimeType): void
    {
        if (in_array($mimeType, ['image/png', 'image/gif', 'image/webp'], true)) {
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
            $transparent = imagecolorallocatealpha($targetImage, 0, 0, 0, 127);
            imagefilledrectangle($targetImage, 0, 0, imagesx($targetImage), imagesy($targetImage), $transparent);

            return;
        }

        $background = imagecolorallocate($targetImage, 255, 255, 255);
        imagefilledrectangle($targetImage, 0, 0, imagesx($targetImage), imagesy($targetImage), $background);
    }

    /**
     * Encode a GD image into binary data using the source mime type.
     */
    private function imageToString(\GdImage $image, ?string $mimeType, int $quality = 88): ?string
    {
        ob_start();

        try {
            $saved = match ($mimeType) {
                'image/png' => imagepng($image, null, 6),
                'image/gif' => imagegif($image),
                'image/webp' => function_exists('imagewebp')
                    ? imagewebp($image, null, min($quality, 92))
                    : imagejpeg($image, null, $quality),
                default => imagejpeg($image, null, $quality),
            };
        } catch (\Throwable) {
            ob_end_clean();

            return null;
        }

        $data = ob_get_clean();

        return ($saved && $data !== false && $data !== '') ? $data : null;
    }
}
