<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organization_type_id',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'logo_path',
        'website_url',
        'manager_name_ar',
        'manager_name_en',
        'manager_message_ar',
        'manager_message_en',
        'manager_image_path',
    ];

    public function organizationType(): BelongsTo
    {
        return $this->belongsTo(OrganizationType::class);
    }
}
