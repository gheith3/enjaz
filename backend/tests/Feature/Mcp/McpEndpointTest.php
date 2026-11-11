<?php

namespace Tests\Feature\Mcp;

use App\Models\McpUsage;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;
use Illuminate\Testing\TestResponse;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;

/**
 * POST /mcp: auth:api -> EnsureMcpAccess -> LogMcpUsage -> throttle:mcp, and the OAuth discovery routes.
 */
class McpEndpointTest extends McpTestCase
{
    /** @return array<string, mixed> */
    private function listTools(): array
    {
        return ['jsonrpc' => '2.0', 'id' => 1, 'method' => 'tools/list', 'params' => []];
    }

    private function postMcp(array $body = []): TestResponse
    {
        return $this->postJson('/mcp', $body ?: $this->listTools(), ['Accept' => 'application/json, text/event-stream']);
    }

    public function test_a_token_is_required(): void
    {
        $this->postMcp()->assertUnauthorized()->assertHeader('WWW-Authenticate');
        $this->assertSame(0, McpUsage::query()->count());
    }

    public function test_the_endpoint_is_absent_when_disabled(): void
    {
        config(['injazat.mcp.enabled' => false]);
        Passport::actingAs($this->admin);

        $this->postMcp()->assertNotFound();
    }

    public function test_an_editor_is_denied(): void
    {
        Passport::actingAs($this->userWithRole('editor'));

        $this->postMcp()->assertForbidden()->assertJsonPath('code', 'mcp_access_denied');
    }

    public function test_a_user_without_a_dashboard_role_is_denied_even_with_the_permission(): void
    {
        $user = $this->userWithRole(null);
        $user->givePermissionTo('Use:Mcp');
        Passport::actingAs($user);

        $this->postMcp()->assertForbidden();
    }

    public function test_the_ip_allowlist_is_enforced(): void
    {
        config(['injazat.mcp.allowed_ips' => ['10.0.0.0/8']]);
        Passport::actingAs($this->admin);

        $this->postMcp()->assertForbidden()->assertJsonPath('code', 'ip_not_allowed');
    }

    public function test_an_admin_lists_the_same_tools_as_before_and_the_call_is_logged(): void
    {
        Passport::actingAs($this->admin);

        $names = collect($this->postMcp()->assertOk()->json('result.tools'))->pluck('name')->sort()->values()->all();

        $this->assertSame([
            'append-image-chunk', 'begin-image-upload', 'create-achievement', 'create-achiever', 'delete-achievement',
            'delete-achiever', 'finish-image-upload', 'get-achievement', 'get-achiever', 'list-taxonomies',
            'search-achievements', 'search-achievers', 'update-achievement', 'update-achiever', 'upload-image',
        ], $names);

        $usage = McpUsage::query()->sole();
        $this->assertSame($this->admin->id, $usage->user_id);
        $this->assertSame('tools/list', $usage->method);
        $this->assertFalse($usage->was_error);
    }

    public function test_existing_clients_initialize_with_their_protocol_version(): void
    {
        Passport::actingAs($this->admin);

        $this->postMcp(['jsonrpc' => '2.0', 'id' => 1, 'method' => 'initialize', 'params' => [
            'protocolVersion' => '2025-06-18', 'capabilities' => (object) [], 'clientInfo' => ['name' => 'claude-ai', 'version' => '1.0'],
        ]])->assertOk()
            ->assertJsonPath('result.protocolVersion', '2025-06-18')
            ->assertJsonPath('result.serverInfo.name', 'Injazat');
    }

    public function test_a_tool_call_is_logged_with_its_arguments(): void
    {
        Passport::actingAs($this->admin);

        $this->postMcp(['jsonrpc' => '2.0', 'id' => 7, 'method' => 'tools/call', 'params' => ['name' => 'get-achievement', 'arguments' => ['identifier' => 'missing']]])
            ->assertOk()->assertJsonPath('result.isError', true);

        $usage = McpUsage::query()->sole();
        $this->assertSame('get-achievement', $usage->tool);
        $this->assertSame(['identifier' => 'missing'], $usage->arguments);
        $this->assertTrue($usage->was_error);
    }

    public function test_use_mcp_is_granted_to_super_admin_and_admin_only(): void
    {
        $this->assertTrue(Role::findByName('super_admin', 'web')->hasPermissionTo('Use:Mcp'));
        $this->assertTrue(Role::findByName('admin', 'web')->hasPermissionTo('Use:Mcp'));
        $this->assertFalse(Role::findByName('editor', 'web')->hasPermissionTo('Use:Mcp'));
        $this->assertTrue($this->userWithRole('super_admin')->canUseMcp());
        $this->assertFalse($this->userWithRole('editor')->canUseMcp());
    }

    public function test_use_mcp_can_be_toggled_in_the_role_form(): void
    {
        $this->actingAs($this->userWithRole('super_admin'))
            ->get(RoleResource::getUrl('edit', ['record' => Role::findByName('admin', 'web')]))
            ->assertOk()->assertSee('Use:Mcp');
    }

    public function test_oauth_discovery_metadata_is_published(): void
    {
        $this->getJson('/.well-known/oauth-protected-resource')->assertOk()->assertJsonStructure(['resource', 'authorization_servers']);
        $this->getJson('/.well-known/oauth-authorization-server')->assertOk()
            ->assertJsonPath('authorization_endpoint', url('/oauth/authorize'))
            ->assertJsonPath('registration_endpoint', url('/oauth/register'));
    }

    public function test_the_consent_screen_renders_in_arabic(): void
    {
        $client = (object) ['name' => 'Claude'];
        $html = view('oauth.authorize', ['client' => $client, 'user' => $this->admin, 'authToken' => 'token', 'scopes' => [], 'request' => request()])->render();

        $this->assertStringContainsString('dir="rtl"', $html);
        $this->assertStringContainsString('موافقة', $html);

        $html = view('oauth.authorize', ['client' => $client, 'user' => $this->userWithRole('editor'), 'authToken' => 'token', 'scopes' => [], 'request' => request()])->render();
        $this->assertStringNotContainsString('موافقة', $html);
        $this->assertStringContainsString('Use:Mcp', $html);
    }

    public function test_the_oauth_consent_screen_asks_guests_to_sign_in_then_shows_the_client(): void
    {
        $client = app(ClientRepository::class)->createAuthorizationCodeGrantClient('Claude', ['https://claude.ai/api/mcp/auth_callback']);
        $url = '/oauth/authorize?'.http_build_query([
            'client_id' => $client->getKey(), 'redirect_uri' => 'https://claude.ai/api/mcp/auth_callback', 'response_type' => 'code', 'scope' => 'mcp:use', 'state' => 'abc',
        ]);

        $this->get($url)->assertRedirect(route('filament.admin.auth.login'));

        $this->actingAs($this->admin)->get($url)->assertOk()->assertSee('Claude')->assertSee('موافقة');
    }
}
