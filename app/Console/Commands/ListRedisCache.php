<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class ListRedisCache extends Command
{
    protected $signature = 'cache:list {--clear : Clear all cache keys}';
    protected $description = 'List all Redis cache keys and optionally clear them';

    public function handle(): int
    {
        if ($this->option('clear')) {
            $this->clearAllCache();
            return 0;
        }

        $this->listCache();
        return 0;
    }

    private function listCache(): void
    {
        $redis = Redis::connection('cache');
        $keys = Cache::get('page_cache_keys', []);

        if (empty($keys)) {
            $this->info('No cache keys found.');
            return;
        }

    

        $headers = ['Key', 'Type', 'TTL (seconds)'];
        $rows = [];

        foreach ($keys as $key) {
            $type = $redis->type($key);
            $ttl = $redis->ttl($key);

            $rows[] = [
                $key,
                $type,
                $ttl === -1 ? 'No expiration' : $ttl
            ];
        }

        $this->table($headers, $rows);
        $this->info(sprintf('Total cache keys: %d', count($keys)));
    }

    
    private function clearAllCache(): void
    {
        $redis = Redis::connection();
        $keys = $redis->keys('*');

        if (empty($keys)) {
            $this->info('No cache keys to clear.');
            return;
        }

        $count = count($keys);

        foreach ($keys as $key) {
            $redis->del($key);
            $this->line("Cleared: $key");
        }

        // Also clear Laravel's cache
        Cache::flush();

        $this->info(sprintf('Successfully cleared %d cache keys.', $count));
        $this->info('Application cache has been cleared.');
    }
}
