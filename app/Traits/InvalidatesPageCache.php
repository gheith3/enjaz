<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait InvalidatesPageCache
{
    public function invalidatePageCache(): void
    {
        // Clear cache for the current URL
        $url = request()->fullUrl();
        Cache::forget('page_cache_' . sha1($url));
    }

    // Call this method when your component updates
    protected function clearPageCache(): void
    {
        $this->invalidatePageCache();
    }
} 