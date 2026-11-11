<?php

namespace Tests\Feature\Mcp;

use App\Mcp\Servers\InjazatServer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Mcp\Server\Testing\TestResponse;
use Tests\TestCase;

/**
 * MCP tests: roles seeded like production, the endpoint enabled, tools called as a user.
 */
abstract class McpTestCase extends TestCase
{
    use RefreshDatabase;

    /** A 1x1 PNG. */
    protected const PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

    protected User $admin;

    /** @var array{private: string, public: string}|null RSA pair of this test process (Passport signs tokens with it). */
    private static ?array $passportKeys = null;

    protected function setUp(): void
    {
        parent::setUp();

        config(['injazat.mcp.enabled' => true, 'passport.private_key' => self::passportKeys()['private'], 'passport.public_key' => self::passportKeys()['public']]);
        $this->admin = $this->userWithRole('admin');
    }

    /** @return array{private: string, public: string} */
    private static function passportKeys(): array
    {
        if (self::$passportKeys === null) {
            $key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
            openssl_pkey_export($key, $private);
            self::$passportKeys = ['private' => $private, 'public' => openssl_pkey_get_details($key)['key']];
        }

        return self::$passportKeys;
    }

    /**
     * @param  class-string  $tool
     * @param  array<string, mixed>  $arguments
     */
    protected function tool(string $tool, array $arguments = [], ?User $user = null): TestResponse
    {
        return InjazatServer::actingAs($user ?? $this->admin)->tool($tool, $arguments);
    }

    /**
     * Structured content of a successful tool call.
     *
     * @param  class-string  $tool
     * @param  array<string, mixed>  $arguments
     * @return array<string, mixed>
     */
    protected function content(string $tool, array $arguments = [], ?User $user = null): array
    {
        $content = [];
        $this->tool($tool, $arguments, $user)->assertHasNoErrors()->assertStructuredContent(function ($json) use (&$content) {
            $content = $json->toArray();
            $json->etc();
        });

        return $content;
    }
}
