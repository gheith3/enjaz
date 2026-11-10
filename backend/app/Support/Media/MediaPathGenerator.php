<?php

namespace App\Support\Media;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

/**
 * Every media file lives under `media/` in the OneBox bucket. That prefix (and only that prefix) is
 * anonymously readable, so images are served straight from https://onebox.fahim.om/<bucket>/media/...
 * Private files (contact attachments) are stored elsewhere and never through this generator.
 */
class MediaPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return $this->base($media).'/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->base($media).'/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->base($media).'/responsive/';
    }

    private function base(Media $media): string
    {
        return 'media/'.$media->uuid;
    }
}
