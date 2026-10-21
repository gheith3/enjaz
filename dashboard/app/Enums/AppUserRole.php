<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum AppUserRole: string implements HasColor, HasDescription, HasLabel
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Editor = 'editor';
    case FullMcp = 'full_mcp';

    public function getLabel(): string
    {
        return __('users.roles.'.$this->value);
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::Editor => 'Editor',
            self::FullMcp => 'Full MCP access: manage achievements and achievers through the AI assistant',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::SuperAdmin => 'danger',
            self::Admin => 'danger',
            self::Editor => 'info',
            self::FullMcp => 'warning',
        };
    }

    public static function canAccessAdmin(): array
    {
        return [
            self::SuperAdmin->value,
            self::Admin->value,
            self::Editor->value,
            self::FullMcp->value,
        ];
    }
}
