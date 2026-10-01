<?php

namespace App\Models;

use App\Traits\HasLocalizedAttributes;
use App\Traits\HasPublishing;
use App\Traits\HasUuidWithlog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use HasFactory, HasLocalizedAttributes, HasPublishing, HasUuidWithlog, SoftDeletes;

    protected $table = 'news';

    protected $fillable = [
        'organization_id',
        'title_ar',
        'title_en',
        'excerpt_ar',
        'excerpt_en',
        'body_ar',
        'body_en',
        'cover_image_path',
        'published_at',
        'show_on_about',
        'slug',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'show_on_about' => 'boolean',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
