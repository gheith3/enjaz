<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate for the MCP endpoint, run after `auth:api` so $request->user() is the
 * Passport user: the feature must be enabled, the caller's address must be on
 * the allowlist when one is set, and the user must be active and hold
 * `Use:Mcp`. Runs per request, so removing the permission or suspending the
 * user cuts access at once, even for tokens already issued.
 */
final class EnsureMcpAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        // A disabled MCP looks absent, like an endpoint that was never deployed.
        if (! config('enjaz.mcp.enabled')) {
            abort(404);
        }

        $allowedIps = (array) config('enjaz.mcp.allowed_ips', []);

        if ($allowedIps !== [] && ! IpUtils::checkIp($request->ip() ?? '', $allowedIps)) {
            return $this->deny('ip_not_allowed', 'This address is not allowed to reach the MCP endpoint.');
        }

        $user = $request->user();

        if (! $user instanceof User || $user->status !== UserStatus::Active || ! $user->can('Use:Mcp')) {
            return $this->deny('mcp_access_denied', 'Your account is not allowed to connect AI tools. Ask an administrator for the Use:Mcp permission.');
        }

        return $next($request);
    }

    private function deny(string $code, string $message): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'code' => $code,
            'errors' => (object) [],
        ], 403);
    }
}
