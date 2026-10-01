<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LandingImage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'image_path',
        'image_alt',
        'description_ar',
        'description_en',
        'subtitle_ar',
        'subtitle_en',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
