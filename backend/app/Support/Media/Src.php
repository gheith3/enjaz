<?php

namespace App\Support\Media;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * URL of an image for the page: its WebP conversion when it is made (transparent PNGs keep their transparency, at a
 * fraction of the size), else the original (SVG, GIF, or while the conversion is still queued).
 */
class Src
{
    public static function of(?Media $media, string $conversion = 'large'): ?string
    {
        if (! $media) {
            return null;
        }

        return $media->hasGeneratedConversion($conversion) ? $media->getUrl($conversion) : $media->getUrl();
    }
}
