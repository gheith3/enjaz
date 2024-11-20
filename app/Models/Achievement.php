<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Achievement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'achievement_scope_id',
        'achievement_main_type_id',
        'sub_achievement_type_id',
        "title_ar",
        "description_ar",
        "title_en",
        "description_en",
        'cover_image_path',
        'video_url',
    ];

    public function achievementScope(): BelongsTo
    {
        return $this->belongsTo(AchievementScope::class);
    }

    public function achievementMainType(): BelongsTo
    {
        return $this->belongsTo(AchievementMainType::class);
    }

    public function subAchievementType(): BelongsTo
    {
        return $this->belongsTo(SubAchievementType::class);
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class,
            'achievement_organizations')
            ->withTimestamps();
    }

    public function publicFigures(): BelongsToMany
    {
        return $this->belongsToMany(PublicFigure::class,
            'achievement_public_figures')
            ->withTimestamps();
    }

}
