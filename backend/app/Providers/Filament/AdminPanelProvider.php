<?php

namespace App\Providers\Filament;

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
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
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
            ->brandName('إنجازات')
            ->colors([
                // Design tokens (design/css/main.css): --teal #0C7F89, --gold #A88344
                'primary' => Color::hex('#0C7F89'),
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
}
