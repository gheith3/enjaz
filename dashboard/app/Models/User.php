<?php

namespace App\Models;

use App\Enums\AppUserRole;
use App\Enums\UserStatus;
use App\Traits\HasUuidWithlog;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, HasRoles, HasUuidWithlog, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'country_code',
        'phone_number',
        'avatar_url',
        'status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => UserStatus::class,
        'last_login_at' => 'datetime',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->status === UserStatus::Active
            && $this->hasAnyRole(AppUserRole::canAccessAdmin());
    }

    protected function getFullPhoneNumberAttribute(): ?string
    {
        return $this->country_code . ' ' . $this->phone_number;
    }

    public function getFilamentAvatarUrl(): ?string
    {

        if (empty($this->avatar_url)) {
            return null;
        }

        return Storage::url($this->avatar_url);
        // return Storage::temporaryUrl($this->avatar_url, now()->addMinutes(60));
    }
}
