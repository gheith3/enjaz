<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achiever extends Model
{
    // For now, we'll leave this as a basic model
    // You can add more configuration later as needed
    
    // If you don't have a database table yet, you can use this
    public $timestamps = false;

    // Optional: Define fillable fields if needed
    protected $fillable = [
        'name',
        'english_name',
        'title',
        'profile_image'
    ];
}
