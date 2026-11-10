<?php

namespace App\Models\Concerns;

use Spatie\ResponseCache\Facades\ResponseCache;

/**
 * Public pages are cached as full responses. Any change to content clears that cache so visitors
 * see edits immediately.
 */
trait ClearsResponseCache
{
    public static function bootClearsResponseCache(): void
    {
        $clear = static fn () => config('responsecache.enabled') ? ResponseCache::clear() : null;

        static::saved($clear);
        static::deleted($clear);
    }
}
