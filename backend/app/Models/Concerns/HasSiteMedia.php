<?php

namespace App\Models\Concerns;

use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Media library with the site's standard WebP conversions and per-image focal point / alt / credit
 * (custom properties edited in the dashboard).
 */
trait HasSiteMedia
{
    use InteractsWithMedia;

    public function registerMediaConversions(?Media $media = null): void
    {
        if ($media && in_array($media->mime_type, ['image/svg+xml', 'image/gif'], true)) {
            return;
        }

        $this->addMediaConversion('card')->format('webp')->width(480)->quality(82);
        $this->addMediaConversion('large')->format('webp')->width(1400)->quality(82);
    }

    public function image(string $collection): ?Media
    {
        return $this->getFirstMedia($collection);
    }

    /**
     * URL of the first image of a collection in a conversion (falls back to the original while the
     * conversion is still being generated).
     */
    public function imageUrl(string $collection, string $conversion = 'large'): ?string
    {
        $media = $this->getFirstMedia($collection);
        if (! $media) {
            return null;
        }

        return $media->hasGeneratedConversion($conversion) ? $media->getUrl($conversion) : $media->getUrl();
    }

    /** CSS object-position for an image ("50% 20%"), default centre. */
    public static function focal(?Media $media): string
    {
        return $media?->getCustomProperty('focal') ?: '50% 50%';
    }

    public static function alt(?Media $media, string $fallback = ''): string
    {
        return (string) ($media?->getCustomProperty('alt') ?: $fallback);
    }
}
