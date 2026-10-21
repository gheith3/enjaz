# MCP server

The dashboard exposes an authenticated [MCP](https://modelcontextprotocol.io) server (package `laravel/mcp`) so AI assistants can manage Injazat content — achievements and achievers (public figures) — on behalf of a user.

## Endpoint

`POST /mcp` (registered in `dashboard/routes/ai.php`, auto-loaded by the package). Middleware chain: `auth:api` (Passport) → `EnsureMcpAccess` → `LogMcpUsage` → `throttle:mcp` (120 requests/min per user).

## Enabling

The endpoint answers 404 unless enabled:

```env
ENJAZ_MCP_ENABLED=true        # default false
ENJAZ_MCP_ALLOWED_IPS=        # optional comma-separated IPs/CIDRs; empty = any
ENJAZ_MCP_PERSONAL_TOKENS=true
```

## Granting a user

A caller must be an **active** user with the `Use:Mcp` permission (Shield/spatie, guard `web`). `RolesSeeder` creates the permission and grants it to `super_admin` and `admin` (never `editor`). Grant it per user or role in the dashboard (Administration → Roles) as usual.

## Authentication

- **OAuth 2.1** (recommended for AI clients): `Mcp::oauthRoutes()` publishes discovery metadata and dynamic client registration; the consent screen is `resources/views/oauth/authorize.blade.php` (registered via `Passport::authorizationView` in `PassportServiceProvider`). Access tokens live 1 hour, refresh tokens 30 days.
- **Personal access tokens** (`config('enjaz.mcp.personal_tokens')`): mint in tinker — `$user->createToken('mcp')->accessToken` — and send as `Authorization: Bearer …`. They live 90 days.

Every JSON-RPC message is audited in `mcp_usages` (method, tool, truncated arguments, error state, duration, IP).

## Tools

| Tool | Permission | Notes |
|---|---|---|
| `list-taxonomies` | `ViewAny:Achievement` or `ViewAny:PublicFigure` | scopes, main types, sub types (id/slug/title ar+en) — call before writes |
| `search-achievements` | `ViewAny:Achievement` | q/scope/main_type/published filters, limit |
| `get-achievement` | `View:Achievement` | by slug or id, incl. linked achiever/organization slugs |
| `create-achievement` | `Create:Achievement` | `title_ar` required; slug auto; taxonomies by slug; `achievers`/`organizations` slug arrays |
| `update-achievement` | `Update:Achievement` | by slug; partial; optional re-sync of links |
| `delete-achievement` | `Delete:Achievement` | soft delete |
| `search-achievers` | `ViewAny:PublicFigure` | q on name ar/en, main_type filter |
| `get-achiever` | `View:PublicFigure` | by slug or id, incl. linked achievement slugs |
| `create-achiever` | `Create:PublicFigure` | `name_ar` required; flags `is_featured`/`in_home_carousel`; `achievements` slug array |
| `update-achiever` | `Update:PublicFigure` | by slug; partial |
| `delete-achiever` | `Delete:PublicFigure` | soft delete |

Server class: `app/Mcp/Servers/InjazatServer.php`; tools under `app/Mcp/Tools/` extending `InjazatTool` (auth, permission checks, structured responses). Record UUIDs are never accepted from input; slug conflicts fail validation.
