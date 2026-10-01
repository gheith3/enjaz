<?php

namespace App\Models;

use App\Enums\ContactMessageStatus;
use App\Traits\HasUuidWithlog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUsMessage extends Model
{
    use HasFactory, HasUuidWithlog;

    protected $fillable = [
        'name',
        'email',
        'message',
        'ip_address',
        'status',
    ];

    protected $casts = [
        'status' => ContactMessageStatus::class,
    ];
}
