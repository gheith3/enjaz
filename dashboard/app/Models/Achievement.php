<?php

namespace App\Models;

use App\Traits\HasLocalizedAttributes;
use App\Traits\HasPublishing;
use App\Traits\HasUuidWithlog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Achievement extends Model
{
    use HasFactory, HasLocalizedAttributes, HasPublishing, HasUuidWithlog, SoftDeletes;

    protected $fillable = [
        'achievement_scope_id',
        'achievement_main_type_id',
        'sub_achievement_type_id',
        'title_ar',
        'title_en',
        'excerpt_ar',
        'excerpt_en',
        'description_ar',
        'description_en',
        'cover_image_path',
        'video_url',
        'achieved_at',
        'country_ar',
        'country_en',
        'slug',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'achieved_at' => 'date',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
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

    public function publicFigures(): BelongsToMany
    {
        return $this->belongsToMany(PublicFigure::class, 'achievement_public_figure')
            ->using(Pivots\AchievementPublicFigure::class)
            ->withTimestamps();
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'achievement_organization')
            ->using(Pivots\AchievementOrganization::class)
            ->withTimestamps();
    }
}
