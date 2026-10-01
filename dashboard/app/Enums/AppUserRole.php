<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum AppUserRole: string implements HasColor, HasDescription, HasLabel
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';


    public function getLabel(): string
    {
        return __('users.roles.'.$this->value);
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::SuperAdmin => 'danger',
            self::Admin => 'danger',
        };
    }

    public static function canAccessAdmin(): array
    {
        return [
            self::SuperAdmin->value,
            self::Admin->value,
        ];
    }
}
