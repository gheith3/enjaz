<?php

namespace App\Providers;

use App\Policies\ActivityPolicy;
use App\Policies\RolePolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;
use Spatie\LaravelSettings\Events\SettingsSaved;
use Spatie\Permission\Models\Role;
use Spatie\ResponseCache\Facades\ResponseCache;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // TLS ends at Cloudflare; the app itself is reached over plain HTTP inside Docker.
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Shield policies for models outside app/Models.
        Gate::policy(Activity::class, ActivityPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);

        // Settings appear on every page (header, footer, meta): clear the page cache when they change.
        Event::listen(SettingsSaved::class, function (): void {
            if (config('responsecache.enabled')) {
                ResponseCache::clear();
            }
        });
    }
}
