<?php

namespace App\Http\Middleware;

use App\Settings\SiteLockSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * While the site is locked before the launch («قفل الموقع قبل التدشين»), visitors get the «قريبًا» page.
 * Open: signed-in dashboard users, visitors who entered the code (cookie), the dashboard, the achievers' review links,
 * the MCP / OAuth endpoints and the built assets.
 */
class SiteLock
{
    public const COOKIE = 'injazat_pass';

    /** Paths that never get the lock page. */
    private const OPEN = [
        'admin', 'admin/*', 'livewire/*', 'livewire-*', 'review/*', 'unlock', 'up', 'build/*', 'storage/*', 'css/*', 'js/*',
        'fonts/*', 'filament/*', 'mcp', 'mcp/*', 'oauth/*', '.well-known/*', 'horizon', 'horizon/*', 'favicon.ico', 'robots.txt',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $settings = self::settings();
        if (! $settings || ! $settings->enabled || blank($settings->code) || $request->is(...self::OPEN)
            || $request->user() || self::passes($request, $settings->code)) {
            return $next($request);
        }

        return response()->view('site.coming-soon', [], 200)
            ->header('X-Robots-Tag', 'noindex, nofollow')
            ->header('Cache-Control', 'no-store, private');
    }

    public static function token(string $code): string
    {
        return hash_hmac('sha256', 'site-lock|'.$code, (string) config('app.key'));
    }

    public static function passes(Request $request, string $code): bool
    {
        return hash_equals(self::token($code), (string) $request->cookie(self::COOKIE));
    }

    /** The settings, or null while their migration has not run yet. */
    public static function settings(): ?SiteLockSettings
    {
        try {
            return app(SiteLockSettings::class);
        } catch (Throwable) {
            return null;
        }
    }
}
