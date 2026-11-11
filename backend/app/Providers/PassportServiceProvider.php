<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

/**
 * Passport exists for one purpose only: letting dashboard users connect AI assistants (MCP, docs/mcp.md)
 * with their own account. Token lifetimes and the consent screen live here.
 */
class PassportServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // OAuth access tokens last 1 hour, refresh tokens 30 days, personal tokens 90 days.
        Passport::tokensExpireIn(now()->addHour());
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addDays(90));

        // Without a registered view the consent screen answers a 500.
        Passport::authorizationView('oauth.authorize');
    }
}
