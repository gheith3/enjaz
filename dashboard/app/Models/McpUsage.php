<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One JSON-RPC message handled by the MCP endpoint.
 */
class McpUsage extends Model
{
    use HasUuids, Prunable;

    public const RETENTION_DAYS = 30;

    public const CREATED_AT = 'occurred_at';

    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'token_id',
        'client_name',
        'method',
        'tool',
        'arguments',
        'was_error',
        'error_message',
        'duration_ms',
        'ip',
        'occurred_at',
    ];

    protected $casts = [
        'arguments' => 'array',
        'was_error' => 'boolean',
        'duration_ms' => 'integer',
        'occurred_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function prunable(): Builder
    {
        return static::query()->where('occurred_at', '<', now()->subDays(self::RETENTION_DAYS));
    }
}
