<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ContactMessageStatus: string implements HasColor, HasLabel
{
    case New = 'new';
    case Read = 'read';
    case InProgress = 'in_progress';
    case Completed = 'completed';

    public function getLabel(): string
    {
        return __('contact.status.'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::New => 'info',
            self::Read => 'gray',
            self::InProgress => 'warning',
            self::Completed => 'success',
        };
    }
}
