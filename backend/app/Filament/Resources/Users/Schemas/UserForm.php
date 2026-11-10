<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->columnSpanFull()->schema([
                TextInput::make('name')->label('الاسم')->required()->maxLength(255),
                TextInput::make('email')->label('البريد الإلكتروني')->email()->required()->maxLength(255)
                    ->unique(ignoreRecord: true)->extraInputAttributes(['dir' => 'ltr']),
                TextInput::make('password')->label('كلمة المرور')->password()->revealable()
                    ->rule('min:12')
                    ->required(fn (string $operation) => $operation === 'create')
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->helperText(fn (string $operation) => $operation === 'edit' ? 'اتركها فارغة للإبقاء على كلمة المرور الحالية.' : null),
                Select::make('roles')->label('الدور')->relationship('roles', 'name')->multiple()->preload()->required(),
            ]),
        ]);
    }
}
