<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AchievementScope extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "title_ar",
        "title_en"
    ];
}
