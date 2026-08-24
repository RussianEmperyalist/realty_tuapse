<?php

namespace App\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaStorage
{
    /** Logical prefix stored in the database for media managed by the app. */
    private const MANAGED_PREFIX = 'storage/';

    /**
     * Disk used for uploaded media ("public" locally, "s3" in production).
     */
    public static function disk(): Filesystem
    {
        return Storage::disk((string) config('realty.media_disk', 'public'));
    }

    /**
     * Whether the stored path belongs to app-managed media ("storage/...").
     */
    public static function isManagedPath(?string $path): bool
    {
        return $path !== null && str_starts_with($path, self::MANAGED_PREFIX);
    }

    /**
     * Convert a stored "storage/<key>" path into a disk key.
     */
    public static function keyOf(string $path): string
    {
        return ltrim(Str::after($path, self::MANAGED_PREFIX), '/');
    }

    /**
     * Public URL for a stored path.
     */
    public static function url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (preg_match('~^https?://~i', $path) === 1) {
            return $path;
        }

        if (self::isManagedPath($path)) {
            return self::disk()->url(self::keyOf($path));
        }

        return asset($path);
    }

    /**
     * Whether a stored path resolves to an existing file.
     */
    public static function exists(?string $path): bool
    {
        if ($path === null || $path === '') {
            return false;
        }

        if (preg_match('~^https?://~i', $path) === 1) {
            return true;
        }

        if (self::isManagedPath($path)) {
            return self::disk()->exists(self::keyOf($path));
        }

        $rawPath = public_path(ltrim($path, '/'));
        $decodedPath = public_path(ltrim(rawurldecode($path), '/'));

        return is_file($rawPath) || is_file($decodedPath);
    }

    /**
     * Delete a managed file referenced by a stored "storage/..." path.
     * Paths outside the managed prefix (e.g. legacy assets) are ignored.
     */
    public static function deleteFromPath(?string $path): void
    {
        if (! self::isManagedPath($path)) {
            return;
        }

        self::disk()->delete(self::keyOf($path));
    }
}
