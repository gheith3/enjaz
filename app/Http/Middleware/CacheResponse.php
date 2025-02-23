<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    public function handle(Request $request, Closure $next): Response
    {

        try {

            // Bypass caching for authenticated users, non-GET requests, API routes, and Livewire components
            if ($request->user() 
            || !$request->isMethod('GET') 
            || $request->is('api/*') 
            || str_contains($request->url(), 'livewire')) {
                return $next($request);
            }



            $cacheKey = 'page_cache_' . sha1($request->fullUrl());

            if (Cache::has($cacheKey)) {
                return response(Cache::get($cacheKey))
                    ->header('X-Cache', 'HIT')
                    ->header('Cache-Control', 'public, max-age=3600');
            }

            $response = $next($request);

            if ($response->status() === 200) {
                Cache::put($cacheKey, $response->getContent(), now()->addHours(1));

                // Store cache key for later invalidation
                $keys = Cache::get('page_cache_keys', []);
                if (!in_array($cacheKey, $keys)) {
                    $keys[] = $cacheKey;
                    Cache::put('page_cache_keys', $keys, now()->addWeek());
                }
            }

            return $response->header('X-Cache', 'MISS')
                ->header('Cache-Control', 'public, max-age=3600');
        } catch (\Exception $e) {
            return $next($request);
        }
    }
}
