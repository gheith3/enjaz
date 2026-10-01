<?php

namespace App\Models;

use App\Traits\HasLocalizedAttributes;
use App\Traits\HasPublishing;
use App\Traits\HasUuidWithlog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubAchievementType extends Model
{
    use HasFactory, HasLocalizedAttributes, HasPublishing, HasUuidWithlog, SoftDeletes;

    protected $fillable = [
        'achievement_main_type_id',
        'title_ar',
        'title_en',
        'slug',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function achievementMainType(): BelongsTo
    {
        return $this->belongsTo(AchievementMainType::class);
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class);
    }
}
