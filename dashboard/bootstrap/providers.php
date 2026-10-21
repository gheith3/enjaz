<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\DashboardPanelProvider;
use App\Providers\LanguageSwitchProvider;
use App\Providers\PassportServiceProvider;

return [
    AppServiceProvider::class,
    LanguageSwitchProvider::class,
    DashboardPanelProvider::class,
    PassportServiceProvider::class,
];
