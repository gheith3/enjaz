<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Joaopaulolndev\FilamentEditProfile\Livewire\BrowserSessionsForm;
use Joaopaulolndev\FilamentEditProfile\Livewire\EditPasswordForm;
use Joaopaulolndev\FilamentEditProfile\Livewire\EditProfileForm;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        $url = env('APP_URL', 'http://localhost:5501');
        if (str_starts_with($url, 'https://')) {
            URL::forceScheme('https');
        }

        // Gate::policy(Activity::class, ActivityPolicy::class);


        // Manually register FilamentEditProfile Livewire components
        Livewire::component('edit_password_form', EditPasswordForm::class);
        Livewire::component('edit_profile_form', EditProfileForm::class);
        Livewire::component('browser_sessions_form', BrowserSessionsForm::class);
    }
}
