<?php

namespace App\Models;

use App\Traits\HasActiveColumn;
use App\Traits\HasLocalizedAttributes;
use App\Traits\HasPublishing;
use App\Traits\HasUuidWithlog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImageMedia extends Model
{
    use HasActiveColumn, HasFactory, HasLocalizedAttributes, HasPublishing, HasUuidWithlog, SoftDeletes;

    protected $table = 'image_media';

    protected $fillable = [
        'public_figure_id',
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'images',
        'is_active',
        'slug',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'images' => 'array',
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function publicFigure(): BelongsTo
    {
        return $this->belongsTo(PublicFigure::class);
    }
}
