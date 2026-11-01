<?php

declare(strict_types=1);

namespace App\Mcp;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Cache-backed state for chunked MCP image uploads. A session lives 30 minutes,
 * is bound to the authenticated MCP user (the cache key carries the user id) and
 * accumulates verified chunks until finish-image-upload assembles them.
 */
final class ImageUploadSession
{
    public const int TTL_SECONDS = 30 * 60;

    private const string PREFIX = 'mcp-image-upload';

    /**
     * @param  array<string, mixed>  $data
     */
    public static function create(int|string $userId, array $data): string
    {
        $uploadId = (string) Str::uuid();

        Cache::put(self::key($userId, $uploadId), $data + ['chunks' => []], self::TTL_SECONDS);

        return $uploadId;
    }

    /**
     * @return array<string, mixed>|null null for unknown, expired or foreign upload ids
     */
    public static function get(int|string $userId, string $uploadId): ?array
    {
        $session = Cache::get(self::key($userId, $uploadId));

        return is_array($session) ? $session : null;
    }

    /**
     * @param  array<string, mixed>  $session
     */
    public static function put(int|string $userId, string $uploadId, array $session): void
    {
        Cache::put(self::key($userId, $uploadId), $session, self::TTL_SECONDS);
    }

    public static function forget(int|string $userId, string $uploadId): void
    {
        Cache::forget(self::key($userId, $uploadId));
    }

    private static function key(int|string $userId, string $uploadId): string
    {
        return self::PREFIX.':'.$userId.':'.$uploadId;
    }
}
