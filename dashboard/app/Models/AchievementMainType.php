<?php

namespace App\Models;

use App\Traits\HasLocalizedAttributes;
use App\Traits\HasPublishing;
use App\Traits\HasUuidWithlog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AchievementMainType extends Model
{
    use HasFactory, HasLocalizedAttributes, HasPublishing, HasUuidWithlog, SoftDeletes;

    protected $fillable = [
        'title_ar',
        'title_en',
        'icon_path',
        'show_on_home',
        'slug',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'show_on_home' => 'boolean',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function subAchievementTypes(): HasMany
    {
        return $this->hasMany(SubAchievementType::class);
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class);
    }

    public function publicFigures(): HasMany
    {
        return $this->hasMany(PublicFigure::class);
    }
}
