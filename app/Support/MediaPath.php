<?php

namespace App\Support;

class MediaPath
{
    /**
     * Convert a stored media path to a browser-safe URL.
     */
    public static function url(?string $path, ?string $fallback = null): ?string
    {
        if ($path === null || $path === '') {
            return $fallback !== null ? self::url($fallback) : null;
        }

        if (preg_match('~^https?://~i', $path) === 1) {
            return $path;
        }

        if (! MediaStorage::exists($path)) {
            return $fallback !== null ? self::url($fallback) : null;
        }

        return MediaStorage::url($path);
    }

    /**
     * Check if a stored media path resolves to an existing public file.
     */
    public static function exists(?string $path): bool
    {
        return MediaStorage::exists($path);
    }
}
