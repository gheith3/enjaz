<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\Contracts\OAuthenticatable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, OAuthenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * Pin permission checks to the web guard, so `can()` gives the same answer when the request is
     * authenticated through the Passport `api` guard (MCP).
     */
    protected string $guard_name = 'web';

    /** Roles that may sign in to the dashboard (permissions per role: Shield, "الأدوار"). */
    public const PANEL_ROLES = ['super_admin', 'admin', 'editor'];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(self::PANEL_ROLES);
    }

    /**
     * May connect AI assistants (MCP, docs/mcp.md): a dashboard user (users have no "active" flag; removing
     * every dashboard role is how an account is switched off) holding Use:Mcp (super_admin and admin by default).
     */
    public function canUseMcp(): bool
    {
        return $this->hasAnyRole(self::PANEL_ROLES) && $this->can('Use:Mcp');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
