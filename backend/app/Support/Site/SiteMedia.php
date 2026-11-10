<?php

namespace App\Support\Site;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Vite;

/**
 * URLs for images stored by the dashboard (paths on the media disk, OneBox under media/) with a
 * bundled design fallback (resources/site/assets/...).
 */
class SiteMedia
{
    public static function url(?string $path, ?string $fallback = null): ?string
    {
        if (filled($path)) {
            return Storage::disk(config('media-library.disk_name'))->url($path);
        }

        return $fallback ? Vite::asset('resources/site/assets/'.$fallback) : null;
    }
}
