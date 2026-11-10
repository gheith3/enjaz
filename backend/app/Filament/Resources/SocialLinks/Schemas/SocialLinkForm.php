<?php

namespace App\Filament\Resources\SocialLinks\Schemas;

use App\Models\SocialLink;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SocialLinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->columnSpanFull()->schema([
                Select::make('platform')->label('المنصة')->required()
                    ->options(collect(SocialLink::PLATFORMS)->mapWithKeys(fn ($p) => [$p => __('site.social.'.$p)])),
                TextInput::make('handle')->label('اسم الحساب')->maxLength(255)->placeholder('@injazat_oman')
                    ->extraInputAttributes(['dir' => 'ltr']),
                TextInput::make('url')->label('الرابط')->url()->maxLength(255)
                    ->helperText('اتركه فارغًا ليظهر الرمز في التذييل كـ«غير متوفر».')
                    ->extraInputAttributes(['dir' => 'ltr']),
                Toggle::make('is_active')->label('ظاهر')->default(true),
            ]),
        ]);
    }
}
