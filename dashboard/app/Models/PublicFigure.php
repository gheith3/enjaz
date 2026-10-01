<?php

namespace App\Models;

use App\Traits\HasLocalizedAttributes;
use App\Traits\HasPublishing;
use App\Traits\HasUuidWithlog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PublicFigure extends Model
{
    use HasFactory, HasLocalizedAttributes, HasPublishing, HasUuidWithlog, SoftDeletes;

    protected $fillable = [
        'achievement_main_type_id',
        'name_ar',
        'name_en',
        'headline_ar',
        'headline_en',
        'description_ar',
        'description_en',
        'story_ar',
        'story_en',
        'image_path',
        'tile_image_path',
        'cutout_image_path',
        'banner_image_path',
        'is_featured',
        'in_home_carousel',
        'linkedin_url',
        'twitter_url',
        'instagram_url',
        'facebook_url',
        'whatsapp_number',
        'email',
        'phone',
        'slug',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'in_home_carousel' => 'boolean',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function achievementMainType(): BelongsTo
    {
        return $this->belongsTo(AchievementMainType::class);
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class, 'achievement_public_figure')
            ->withTimestamps();
    }

    public function imageMedia(): HasMany
    {
        return $this->hasMany(ImageMedia::class);
    }

    public function videoMedia(): HasMany
    {
        return $this->hasMany(VideoMedia::class);
    }
}
