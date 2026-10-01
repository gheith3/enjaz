<?php

declare(strict_types=1);

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;

class AchievementOrganization extends Pivot
{
    protected $table = 'achievement_organization';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (self $pivot) {
            $pivot->id ??= (string) Str::uuid();
        });
    }
}
