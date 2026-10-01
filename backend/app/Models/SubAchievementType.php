<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubAchievementType extends Model
{
    use softDeletes;
    protected $fillable = [
        'achievement_main_type_id',
        'title_ar',
        'title_en',
    ];

    public function achievement_main_type(): BelongsTo
    {
        return $this->belongsTo(AchievementMainType::class);
    }

}
