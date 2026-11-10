<?php

namespace App\Filament\Pages;

use App\Filament\Support\Translatable;
use App\Settings\GeneralSettings;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageGeneralSettings extends SettingsPage
{
    use HasPageShield;

    protected static string $settings = GeneralSettings::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'الإعدادات';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'هوية الموقع والتواصل';

    protected static ?string $title = 'هوية الموقع والتواصل';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('الهوية')->columns(2)->columnSpanFull()->schema([
                Translatable::text('site_name', 'اسم الموقع'),
                Translatable::textarea('tagline', 'الشعار النصي', rows: 2),
                self::image('logo', 'الشعار (SVG أو PNG شفاف)'),
                self::image('favicon', 'أيقونة المتصفح'),
                ColorPicker::make('theme_color')->label('لون شريط المتصفح في الهاتف')->required(),
            ]),
            Section::make('التواصل (التذييل وصفحة تواصل معنا)')->columns(2)->columnSpanFull()->schema([
                TagsInput::make('phones')->label('أرقام الهاتف')->placeholder('+968 ...')
                    ->helperText('اكتب الرقم ثم اضغط Enter. الأول يظهر في التذييل.'),
                TextInput::make('email')->label('البريد الإلكتروني')->email()->extraInputAttributes(['dir' => 'ltr']),
                Translatable::text('address', 'العنوان', required: false),
                Translatable::text('copyright', 'نص الحقوق', required: false),
            ]),
        ]);
    }

    public static function image(string $name, string $label): FileUpload
    {
        return FileUpload::make($name)->label($label)
            ->disk(config('media-library.disk_name'))->directory('media/settings')->visibility('public')
            ->image()->acceptedFileTypes(['image/svg+xml', 'image/png', 'image/jpeg', 'image/webp', 'image/x-icon'])
            ->maxSize(4096);
    }
}
