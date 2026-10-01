<?php

namespace App\Models;

use App\Traits\HasActiveColumn;
use App\Traits\HasLocalizedAttributes;
use App\Traits\HasPublishing;
use App\Traits\HasUuidWithlog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PublicationMedia extends Model
{
    use HasActiveColumn, HasFactory, HasLocalizedAttributes, HasPublishing, HasUuidWithlog, SoftDeletes;

    protected $table = 'publication_media';

    protected $fillable = [
        'title_ar',
        'title_en',
        'file_path',
        'description_ar',
        'description_en',
        'cover_image_path',
        'is_active',
        'slug',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];
}
