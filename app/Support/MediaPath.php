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

        if (! self::exists($path)) {
            return $fallback !== null ? self::url($fallback) : null;
        }

        $segments = array_map(
            static fn (string $segment): string => rawurlencode(rawurldecode($segment)),
            explode('/', ltrim($path, '/')),
        );

        return asset(implode('/', $segments));
    }

    /**
     * Check if a stored media path resolves to an existing public file.
     */
    public static function exists(?string $path): bool
    {
        if ($path === null || $path === '') {
            return false;
        }

        if (preg_match('~^https?://~i', $path) === 1) {
            return true;
        }

        $rawPath = public_path(ltrim($path, '/'));
        $decodedPath = public_path(ltrim(rawurldecode($path), '/'));

        return is_file($rawPath) || is_file($decodedPath);
    }
}
