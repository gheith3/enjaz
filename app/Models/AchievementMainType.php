<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AchievementMainType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "title_ar",
        "title_en"
    ];

    public function subTypes(): HasMany
    {
        return $this->hasMany(SubAchievementType::class);
    }
}
