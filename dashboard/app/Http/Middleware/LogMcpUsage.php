<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\McpUsage;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\AccessToken as PassportAccessToken;
use Laravel\Passport\Token as PassportToken;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

/**
 * Writes one MCP usage row per JSON-RPC message after the response, including
 * batch requests. Add it to the MCP route in this order:
 *
 * ->middleware(['auth:api', EnsureMcpAccess::class, LogMcpUsage::class, 'throttle:mcp'])
 *
 * Streamed responses (including SSE) are never read or consumed; their error
 * state comes from the HTTP status alone. Logging failures are only warnings.
 */
final class LogMcpUsage
{
    private const MAX_ARGUMENT_STRING_LENGTH = 2_000;

    private const MAX_ERROR_MESSAGE_LENGTH = 500;

    public function handle(Request $request, Closure $next): Response
    {
        $request->attributes->set('mcp_usage_started_at', hrtime(true));
        $request->attributes->set('mcp_usage_occurred_at', now());

        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        $user = $request->user();

        if ($user === null) {
            return;
        }

        try {
            $payload = $request->json()->all();
            $messages = $this->messages($payload);

            if ($messages === []) {
                return;
            }

            $isBatch = array_is_list($payload);
            $responseBody = $this->decodeJsonResponse($response);
            $batchResponses = $this->batchResponses($isBatch ? $responseBody : null);
            $startedAt = $request->attributes->get('mcp_usage_started_at');
            $durationMs = $startedAt ? (int) round((hrtime(true) - $startedAt) / 1_000_000) : 0;
            $token = method_exists($user, 'token') ? $user->token() : null;
            $token = $token instanceof PassportAccessToken || $token instanceof PassportToken ? $token : null;
            $tokenId = $token instanceof PassportAccessToken ? $token->oauth_access_token_id : $token?->getKey();
            $client = $token?->client;
            $clientName = $client?->hasGrantType('personal_access')
                ? 'personal token'
                : ($client?->name !== null ? mb_substr((string) $client->name, 0, 255) : null);

            foreach ($messages as $message) {
                $method = $message['method'];
                $params = is_array($message['params'] ?? null) ? $message['params'] : [];
                $isToolCall = $method === 'tools/call';
                $rpcResponse = $isBatch
                    ? $batchResponses[$this->idKey($message['id'] ?? null)] ?? null
                    : $responseBody;

                McpUsage::query()->create([
                    'user_id' => $user->getAuthIdentifier(),
                    'token_id' => $tokenId === null ? null : (string) $tokenId,
                    'client_name' => $clientName,
                    'method' => mb_substr($method, 0, 255),
                    'tool' => $isToolCall && is_string($params['name'] ?? null)
                        ? mb_substr($params['name'], 0, 255)
                        : null,
                    'arguments' => $isToolCall && array_key_exists('arguments', $params)
                        ? $this->truncateStrings($params['arguments'])
                        : null,
                    'was_error' => $this->wasError($response->getStatusCode(), $rpcResponse),
                    'error_message' => $this->errorMessage($response, $rpcResponse, $responseBody),
                    'duration_ms' => $durationMs,
                    'ip' => $request->ip(),
                    'occurred_at' => $request->attributes->get('mcp_usage_occurred_at', now()),
                ]);
            }
        } catch (Throwable $exception) {
            Log::warning('Failed to record MCP usage.', [
                'exception' => $exception,
                'path' => $request->path(),
            ]);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function messages(mixed $payload): array
    {
        if (! is_array($payload)) {
            return [];
        }

        $messages = array_is_list($payload) ? $payload : [$payload];

        return array_values(array_filter(
            $messages,
            fn (mixed $message): bool => is_array($message) && is_string($message['method'] ?? null) && $message['method'] !== '',
        ));
    }

    /**
     * @return array<string, mixed>|list<mixed>|null
     */
    private function decodeJsonResponse(Response $response): ?array
    {
        $contentType = strtolower((string) $response->headers->get('Content-Type'));

        if ($response instanceof StreamedResponse
            || $response instanceof BinaryFileResponse
            || str_contains($contentType, 'text/event-stream')) {
            return null;
        }

        $content = $response->getContent();

        if ($content === false || $content === '') {
            return null;
        }

        $decoded = json_decode($content, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * @param  array<string, mixed>|list<mixed>|null  $responseBody
     * @return array<string, array<string, mixed>>
     */
    private function batchResponses(?array $responseBody): array
    {
        if ($responseBody === null || ! array_is_list($responseBody)) {
            return [];
        }

        $responses = [];

        foreach ($responseBody as $candidate) {
            if (is_array($candidate) && array_key_exists('id', $candidate)) {
                $responses[$this->idKey($candidate['id'])] = $candidate;
            }
        }

        return $responses;
    }

    private function idKey(mixed $id): string
    {
        return gettype($id).':'.(string) $id;
    }

    /**
     * @param  array<string, mixed>|null  $rpcResponse
     */
    private function wasError(int $status, ?array $rpcResponse): bool
    {
        return $status >= 400
            || ($rpcResponse !== null && array_key_exists('error', $rpcResponse) && $rpcResponse['error'] !== null)
            || ($rpcResponse['result']['isError'] ?? false) === true;
    }

    /**
     * @param  array<string, mixed>|null  $rpcResponse
     * @param  array<string, mixed>|list<mixed>|null  $responseBody
     */
    private function errorMessage(Response $response, ?array $rpcResponse, ?array $responseBody): ?string
    {
        if ($rpcResponse !== null && array_key_exists('error', $rpcResponse) && $rpcResponse['error'] !== null) {
            $error = $rpcResponse['error'];
            $message = is_array($error) ? $error['message'] ?? null : $error;

            return $this->shortError(is_string($message) ? $message : 'JSON-RPC error');
        }

        if (($rpcResponse['result']['isError'] ?? false) === true) {
            $content = $rpcResponse['result']['content'] ?? [];

            if (is_array($content)) {
                foreach ($content as $item) {
                    if (is_array($item) && is_string($item['text'] ?? null)) {
                        return $this->shortError($item['text']);
                    }
                }
            }

            return 'Tool call failed';
        }

        if ($response->getStatusCode() >= 400) {
            $message = is_array($responseBody) && ! array_is_list($responseBody)
                ? $responseBody['message'] ?? null
                : null;

            return $this->shortError(is_string($message) && $message !== ''
                ? $message
                : 'HTTP '.$response->getStatusCode());
        }

        return null;
    }

    private function shortError(string $message): string
    {
        return mb_substr($message, 0, self::MAX_ERROR_MESSAGE_LENGTH);
    }

    private function truncateStrings(mixed $value): mixed
    {
        if (is_string($value)) {
            return mb_substr($value, 0, self::MAX_ARGUMENT_STRING_LENGTH);
        }

        if (! is_array($value)) {
            return $value;
        }

        foreach ($value as $key => $item) {
            $value[$key] = $this->truncateStrings($item);
        }

        return $value;
    }
}
