<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PublicFigure extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'image_path',
    ];
}
