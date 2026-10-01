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

class Organization extends Model
{
    use HasFactory, HasLocalizedAttributes, HasPublishing, HasUuidWithlog, SoftDeletes;

    protected $fillable = [
        'organization_type_id',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'logo_path',
        'logo_white_path',
        'website_url',
        'manager_name_ar',
        'manager_name_en',
        'manager_message_ar',
        'manager_message_en',
        'manager_image_path',
        'slug',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function organizationType(): BelongsTo
    {
        return $this->belongsTo(OrganizationType::class);
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class, 'achievement_organization')
            ->withTimestamps();
    }

    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }
}
