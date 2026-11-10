<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use App\Filament\Resources\MenuItems\MenuItemResource;
use App\Filament\Support\Translatable;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class MenuItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->columnSpanFull()->schema([
                Select::make('location')->label('المكان')->options(MenuItemResource::locations())
                    ->required()->default('header')->live(),
                TextInput::make('url')->label('الرابط')->required()->maxLength(255)
                    ->datalist(config('site.links'))
                    ->helperText('صفحة في الموقع مثل /achievements، أو رابط كامل يبدأ بـ https://')
                    ->extraInputAttributes(['dir' => 'ltr']),
                Translatable::text('label', 'النص'),
                Toggle::make('show_in_desktop_nav')->label('يظهر في شريط الكمبيوتر العلوي')
                    ->visible(fn (Get $get) => $get('location') === 'header')->live(),
                Toggle::make('opens_new_tab')->label('يفتح في نافذة جديدة'),
                Translatable::text('label_short', 'النص المختصر في شريط الكمبيوتر', required: false)
                    ->visible(fn (Get $get) => $get('location') === 'header' && $get('show_in_desktop_nav')),
                Toggle::make('is_active')->label('ظاهر')->default(true),
            ]),
        ]);
    }
}
