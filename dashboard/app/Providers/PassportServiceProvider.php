<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

/**
 * Passport exists in this app for one purpose only: letting users connect AI
 * assistants (MCP) with their own account. Token lifetimes and the consent
 * screen live here.
 */
class PassportServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // OAuth access tokens last 1 hour, refresh tokens 30 days, personal
        // tokens 90 days.
        Passport::tokensExpireIn(now()->addHour());
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addDays(90));

        // Without a registered view the consent screen answers a 500.
        Passport::authorizationView('oauth.authorize');
    }
}
