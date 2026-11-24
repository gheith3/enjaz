<?php

namespace App\Filament\Pages;

use App\Http\Middleware\SiteLock;
use App\Settings\SiteLockSettings;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/** «قفل الموقع قبل التدشين»: visitors see the «قريبًا» page; the team enters the 6-digit code to see the site. */
class ManageSiteLock extends SettingsPage
{
    use HasPageShield;

    protected static string $settings = SiteLockSettings::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLockClosed;

    protected static string|UnitEnum|null $navigationGroup = 'الإعدادات';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'قفل الموقع قبل التدشين';

    protected static ?string $title = 'قفل الموقع قبل التدشين';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columnSpanFull()->description('عند التفعيل يرى الزوار صفحة «قريبًا» فقط. لوحة الإدارة وروابط مراجعة المنجزين تعمل كالمعتاد، ومن دخل لوحة الإدارة يرى الموقع كاملًا.')
                ->schema([
                    Toggle::make('enabled')->label('الموقع مقفل (صفحة «قريبًا»)')->live(),
                    TextInput::make('code')->label('الرقم السري (٦ أرقام)')->helperText('يُدخله الفريق في صفحة «قريبًا» من زر «دخول» لمشاهدة الموقع. تغييره يُخرج كل من دخل به.')
                        ->required(fn (Get $get) => (bool) $get('enabled'))->regex('/^[0-9٠-٩۰-۹]{6}$/u')
                        ->validationMessages(['regex' => 'الرقم السري ٦ أرقام.'])
                        ->dehydrateStateUsing(fn (?string $state) => filled($state) ? SiteLock::latinDigits($state) : null)
                        ->extraInputAttributes(['dir' => 'ltr', 'inputmode' => 'numeric']),
                ]),
        ]);
    }
}
