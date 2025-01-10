<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoMedia extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'url',
        'description',
        'cover_image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
