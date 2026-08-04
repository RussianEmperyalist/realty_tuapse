<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageStorageService
{
    /**
     * Store a public file and return its browser path.
     */
    public function storePublicFile(UploadedFile $file, string $directory): string
    {
        $directory = trim($directory, '/');
        $extension = $this->resolveOriginalExtension($file);
        $filename = (string) Str::uuid() . '.' . $extension;
        $storedPath = $file->storeAs($directory, $filename, 'public');

        return 'storage/' . ltrim($storedPath, '/');
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
        $extension = $this->resolveOriginalExtension($file);
        $filename = (string) Str::uuid() . '.' . $extension;

        $storedPath = $file->storeAs($directory, $filename, 'public');
        $path = 'storage/' . ltrim($storedPath, '/');
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
     * Generate a thumbnail when GD is available.
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

        $absolutePath = Storage::disk('public')->path(trim($directory, '/') . '/' . $filename);
        $absoluteDirectory = dirname($absolutePath);

        if (! is_dir($absoluteDirectory) && ! @mkdir($absoluteDirectory, 0775, true) && ! is_dir($absoluteDirectory)) {
            imagedestroy($sourceImage);
            imagedestroy($targetImage);

            return null;
        }

        $saved = $this->saveImageResource($targetImage, $absolutePath, $mimeType);

        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        if (! $saved) {
            @unlink($absolutePath);
            Log::warning('Thumbnail generation failed for uploaded image.', [
                'path' => $absolutePath,
                'mime' => $mimeType,
            ]);

            return null;
        }

        return 'storage/' . trim($directory, '/') . '/' . $filename;
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
     * Save the generated GD image using the source mime type.
     */
    private function saveImageResource(\GdImage $image, string $path, ?string $mimeType): bool
    {
        return match ($mimeType) {
            'image/png' => imagepng($image, $path, 6),
            'image/gif' => imagegif($image, $path),
            'image/webp' => function_exists('imagewebp')
                ? imagewebp($image, $path, 88)
                : imagejpeg($image, $path, 88),
            default => imagejpeg($image, $path, 88),
        };
    }
}
