<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile(EditProfile::class, isSimple: false) // "الملف الشخصي" in the user menu: name, email, password (asks the current one)
            ->brandName('إنجازات')
            ->brandLogo(fn () => self::logo())
            ->brandLogoHeight('3.25rem')
            ->topbar(false) // full-height green sidebar (logo on top, user menu at the bottom), as the site's mood
            ->renderHook(PanelsRenderHook::HEAD_END, fn () => Blade::render("@vite('resources/css/admin.css')"))
            ->colors([
                // Design tokens (design/css/main.css): --teal #0C7F89, --gold #A88344
                // Hand-tuned teal: Color::hex() makes it too saturated (bright cyan); 600 ≈ --teal, 700 ≈ --primary
                'primary' => [
                    50 => 'oklch(0.977 0.010 205)', 100 => 'oklch(0.950 0.022 205)', 200 => 'oklch(0.905 0.038 205)',
                    300 => 'oklch(0.840 0.058 205)', 400 => 'oklch(0.750 0.078 205)', 500 => 'oklch(0.640 0.090 205)',
                    600 => 'oklch(0.545 0.092 205)', 700 => 'oklch(0.480 0.082 205)', 800 => 'oklch(0.420 0.070 205)',
                    900 => 'oklch(0.370 0.060 205)', 950 => 'oklch(0.270 0.045 205)',
                ],
                'warning' => Color::hex('#A88344'),
                'gray' => Color::Slate,
            ])
            ->font('Cairo')
            ->sidebarCollapsibleOnDesktop()
            ->globalSearch(false) // titles are translatable jsonb; each table has its own search
            ->maxContentWidth('full')
            ->navigationGroups([
                NavigationGroup::make('المحتوى'),
                NavigationGroup::make('المجلة'),
                NavigationGroup::make('التصنيفات'),
                NavigationGroup::make('الصفحات والقوائم'),
                NavigationGroup::make('الإعدادات'),
                NavigationGroup::make('النظام')->collapsed(),
            ])
            ->navigationItems([
                NavigationItem::make('الموقع')->url('/', shouldOpenInNewTab: true)
                    ->icon(Heroicon::OutlinedGlobeAlt)->sort(-1),
                NavigationItem::make('الطوابير (Horizon)')->url('/horizon', shouldOpenInNewTab: true)
                    ->icon(Heroicon::OutlinedQueueList)->group('النظام')->sort(9)
                    ->visible(fn () => auth()->user()?->hasRole('super_admin')),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make()
                    ->navigationGroup('النظام')
                    ->navigationSort(2),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    /** The site's white logo inline, coloured by CSS (white in the sidebar, teal on the login card). */
    private static function logo(): HtmlString
    {
        $svg = file_get_contents(resource_path('site/assets/svg/logo.svg'));
        $svg = str_replace(['fill="#FFF"', 'id="a"', 'url(#a)', 'width="117" height="94" '],
            ['fill="currentColor"', 'id="inj-logo-clip"', 'url(#inj-logo-clip)', 'role="img" aria-label="إنجازات" '], $svg);

        return new HtmlString($svg);
    }
}
