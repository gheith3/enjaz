<?php

namespace App\Filament\Resources\Redirects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RedirectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->columnSpanFull()->schema([
                TextInput::make('from_path')->label('الرابط القديم')->required()->maxLength(255)
                    ->unique(ignoreRecord: true)->helperText('مثل /profile.html أو /federation.html?org=oman-fa')
                    ->extraInputAttributes(['dir' => 'ltr']),
                TextInput::make('to_path')->label('الرابط الجديد')->required()->maxLength(255)
                    ->extraInputAttributes(['dir' => 'ltr']),
                Select::make('status_code')->label('النوع')->required()->default(301)
                    ->options([301 => 'دائم (301)', 302 => 'مؤقت (302)']),
            ]),
        ]);
    }
}
