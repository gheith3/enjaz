<?php

namespace App\Filament\Pages;

use App\Filament\Support\Translatable;
use App\Settings\SeoSettings;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageSeoSettings extends SettingsPage
{
    use HasPageShield;

    protected static string $settings = SeoSettings::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static string|UnitEnum|null $navigationGroup = 'الإعدادات';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'محركات البحث والمشاركة';

    protected static ?string $title = 'محركات البحث والمشاركة';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->columnSpanFull()->schema([
                Translatable::textarea('default_description', 'الوصف الافتراضي', required: true),
                ManageGeneralSettings::image('default_og_image', 'صورة المشاركة الافتراضية (1200×630)'),
                TextInput::make('title_separator')->label('الفاصل في عنوان الصفحة')->required()
                    ->helperText('مثال: «الإنجازات | إنجازات شباب عُمان»'),
                TextInput::make('og_locale')->label('لغة المشاركة (og:locale)')->required()->extraInputAttributes(['dir' => 'ltr']),
            ]),
        ]);
    }
}
