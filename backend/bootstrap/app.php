<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Behind Cloudflare Tunnel / demo-gateway: trust X-Forwarded-* from the proxy.
        $middleware->trustProxies(at: '*');
        // Read counter sent from cached article pages (their CSRF token may be old); it only increments a number.
        $middleware->validateCsrfTokens(except: ['magazine/*/view']);
        // Guests reaching an auth-only page (the OAuth consent screen of AI assistants) sign in on the dashboard.
        $middleware->redirectGuestsTo(fn () => route('filament.admin.auth.login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*', 'mcp') || $request->expectsJson(),
        );
    })->create();
