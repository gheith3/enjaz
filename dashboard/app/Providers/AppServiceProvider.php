<?php

namespace App\Providers;

use App\Policies\ActivityPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Joaopaulolndev\FilamentEditProfile\Livewire\BrowserSessionsForm;
use Joaopaulolndev\FilamentEditProfile\Livewire\EditPasswordForm;
use Joaopaulolndev\FilamentEditProfile\Livewire\EditProfileForm;
use Livewire\Livewire;
use Spatie\Activitylog\Models\Activity;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        $url = env('APP_URL', 'http://localhost:5501');
        if (str_starts_with($url, 'https://')) {
            URL::forceScheme('https');
        }

        Gate::policy(Activity::class, ActivityPolicy::class);

        // AI agents make bursts of calls; keep a generous but finite per-user bucket.
        RateLimiter::for('mcp', fn (Request $request): Limit => Limit::perMinute(120)
            ->by('mcp:'.($request->user()?->getAuthIdentifier() ?? $request->ip())));

        // Manually register FilamentEditProfile Livewire components
        Livewire::component('edit_password_form', EditPasswordForm::class);
        Livewire::component('edit_profile_form', EditProfileForm::class);
        Livewire::component('browser_sessions_form', BrowserSessionsForm::class);
    }
}
