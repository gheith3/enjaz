<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait InvalidatesPageCache
{
    public function rendered()
    {
        $this->invalidatePageCache();
    }

    public function invalidatePageCache(): void
    {
        // Clear cache for the current URL
        $url = request()->fullUrl();
        Cache::forget('page_cache_' . sha1($url));
    }

    public function updated($property): void
    {
        $this->clearPageCache();
    }
}
