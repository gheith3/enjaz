<?php

declare(strict_types=1);

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;

class AchievementPublicFigure extends Pivot
{
    protected $table = 'achievement_public_figure';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (self $pivot) {
            $pivot->id ??= (string) Str::uuid();
        });
    }
}
