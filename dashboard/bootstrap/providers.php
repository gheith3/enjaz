<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\DashboardPanelProvider;
use App\Providers\LanguageSwitchProvider;

return [
    AppServiceProvider::class,
    LanguageSwitchProvider::class,
    DashboardPanelProvider::class,
];
