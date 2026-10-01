<?php

namespace App\Support;

/**
 * Sized image URLs. Cloudinary uploads are asked for the width they are shown at, in the
 * best format the browser accepts (f_auto: AVIF/WebP) and an automatic quality. The local
 * placeholder photos come in fixed sizes (name-400.jpg, name-800.jpg, and name.jpg at 1200px).
 * Any other source is returned unchanged.
 */
class ImageUrl
{
    private const CLOUDINARY = '~^(https://res\.cloudinary\.com/[^/]+/image/upload/)(.+)$~';

    private const LOCAL = '~^(/images/birds/[a-z0-9-]+)\.jpg$~';

    /** Widths of the local variants; the largest is the unsuffixed file. */
    private const LOCAL_WIDTHS = [400, 800, 1200];

    /** @var array<string, bool> */
    private static array $localVariants = [];

    public static function width(?string $url, int $width): ?string
    {
        if ($base = self::localBase($url)) {
            $chosen = collect(self::LOCAL_WIDTHS)->first(fn (int $size): bool => $size >= $width) ?? max(self::LOCAL_WIDTHS);

            return self::localFile($base, $chosen);
        }

        if (! $url || ! preg_match(self::CLOUDINARY, $url, $parts)) {
            return $url;
        }

        // A URL that already carries a transformation (e.g. "c_fill,w_300/") is left as chosen.
        if (preg_match('~^[a-z]{1,3}_[^/]*/~', $parts[2])) {
            return $url;
        }

        return $parts[1]."f_auto,q_auto,c_limit,w_{$width}/".$parts[2];
    }

    /**
     * A srcset for the given widths, or null when the source cannot be resized.
     *
     * @param  list<int>  $widths
     */
    public static function srcset(?string $url, array $widths): ?string
    {
        if ($base = self::localBase($url)) {
            return collect(self::LOCAL_WIDTHS)->map(fn (int $size): string => self::localFile($base, $size)." {$size}w")->implode(', ');
        }

        if (! $url || ! preg_match(self::CLOUDINARY, $url) || self::width($url, 1) === $url) {
            return null;
        }

        return collect($widths)
            ->unique()
            ->sort()
            ->map(fn (int $width): string => self::width($url, $width)." {$width}w")
            ->implode(', ');
    }

    /** "/images/birds/name" when the local photo has its smaller variants on disk. */
    private static function localBase(?string $url): ?string
    {
        if (! $url || ! preg_match(self::LOCAL, $url, $parts)) {
            return null;
        }

        return (self::$localVariants[$parts[1]] ??= is_file(public_path($parts[1].'-400.jpg'))) ? $parts[1] : null;
    }

    private static function localFile(string $base, int $width): string
    {
        return $width === max(self::LOCAL_WIDTHS) ? "{$base}.jpg" : "{$base}-{$width}.jpg";
    }
}
