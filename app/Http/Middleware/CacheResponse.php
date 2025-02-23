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
        // Don't cache if user is authenticated or if it's not a GET request
        if ($request->user() || !$request->isMethod('GET')) {
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
        }

        return $response->header('X-Cache', 'MISS')
            ->header('Cache-Control', 'public, max-age=3600');
    }
} 