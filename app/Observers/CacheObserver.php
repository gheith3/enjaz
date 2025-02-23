<?php

declare(strict_types=1);

namespace App\Observers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class CacheObserver
{
    public function created(Model $model): void
    {
        $this->clearCache();
    }

    public function updated(Model $model): void
    {
        $this->clearCache();
    }

    public function deleted(Model $model): void
    {
        $this->clearCache();
    }

    public function restored(Model $model): void
    {
        $this->clearCache();
    }

    public function forceDeleted(Model $model): void
    {
        $this->clearCache();
    }

    private function clearCache(): void
    {
        // Clear all page cache
        $keys = Cache::get('page_cache_keys', []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}
