<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Passport\AccessToken;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate of the MCP endpoint, run after `auth:api` so $request->user() is the Passport user: the feature must be
 * enabled, the caller's address must be on the allowlist when one is set, personal access tokens must be allowed
 * when one is used, and the user must be a dashboard user holding `Use:Mcp`. Runs per request, so removing the
 * permission or the user's roles cuts access at once, even for tokens already issued.
 */
final class EnsureMcpAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        // A disabled MCP looks absent, like an endpoint that was never deployed.
        if (! config('injazat.mcp.enabled')) {
            abort(404);
        }

        $allowedIps = (array) config('injazat.mcp.allowed_ips', []);

        if ($allowedIps !== [] && ! IpUtils::checkIp($request->ip() ?? '', $allowedIps)) {
            return $this->deny('ip_not_allowed', 'This address is not allowed to reach the MCP endpoint.');
        }

        $user = $request->user();

        if (! $user instanceof User || ! $user->canUseMcp()) {
            return $this->deny('mcp_access_denied', 'Your account is not allowed to connect AI tools. Ask an administrator for the Use:Mcp permission.');
        }

        if (! config('injazat.mcp.personal_tokens') && $this->usesPersonalToken($user)) {
            return $this->deny('personal_tokens_disabled', 'Personal access tokens are disabled for the MCP endpoint; connect with OAuth.');
        }

        return $next($request);
    }

    private function usesPersonalToken(User $user): bool
    {
        $token = $user->token();

        return $token instanceof AccessToken && (bool) $token->client?->hasGrantType('personal_access');
    }

    private function deny(string $code, string $message): JsonResponse
    {
        return response()->json(['message' => $message, 'code' => $code, 'errors' => (object) []], 403);
    }
}
