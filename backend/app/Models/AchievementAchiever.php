<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Link between an achievement and one of its achievers (edited as a list on the achievement).
 */
class AchievementAchiever extends Model
{
    protected $table = 'achievement_achiever';

    public $timestamps = false;

    protected $fillable = ['achievement_id', 'achiever_id', 'is_primary', 'sort_order'];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean'];
    }

    public function achiever(): BelongsTo
    {
        return $this->belongsTo(Achiever::class);
    }

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class);
    }
}
