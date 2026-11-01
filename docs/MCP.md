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
| `get-achievement` | `View:Achievement` | by slug or id, incl. linked achiever/organization slugs and cover image metadata (url/sha256/bytes/width/height/mime) |
| `create-achievement` | `Create:Achievement` | `title_ar` required; slug auto; taxonomies by slug; `achievers`/`organizations` slug arrays |
| `update-achievement` | `Update:Achievement` | by slug; partial; optional re-sync of links |
| `delete-achievement` | `Delete:Achievement` | soft delete |
| `search-achievers` | `ViewAny:PublicFigure` | q on name ar/en, main_type filter |
| `get-achiever` | `View:PublicFigure` | by slug or id, incl. linked achievement slugs and image slot metadata (url/sha256/bytes/width/height/mime) |
| `create-achiever` | `Create:PublicFigure` | `name_ar` required; flags `is_featured`/`in_home_carousel`; `achievements` slug array |
| `update-achiever` | `Update:PublicFigure` | by slug; partial |
| `delete-achiever` | `Delete:PublicFigure` | soft delete |
| `upload-image` | `Update:Achievement` / `Update:PublicFigure` / `Update:Organization` by `type` | base64 image (optional `data:` prefix, whitespace tolerated) to achievement `cover`, achiever `image`/`tile`/`cutout`/`banner`, or organization `logo`/`logo_white`/`manager_image`; one slot via `field` or several via `fields`; optional `sha256` verified before any write (mismatch ⇒ error with expected/actual hash, nothing stored); max 10 MB, jpeg/png/webp/gif; replaces the previous file |
| `begin-image-upload` | same by `type` | starts a chunked upload for base64 longer than ~8,000 chars: validates record + `fields`, takes whole-file `sha256`, `total_bytes`, `chunk_count`; returns `upload_id` (30 min TTL, bound to the MCP user, cache key `mcp-image-upload:{userId}:{uploadId}`) |
| `append-image-chunk` | (session owner) | stores one verified chunk (`index` 0-based, `data` base64, `chunk_sha256`); recommended ≤ 6,000 base64 chars per chunk; re-sending an index replaces it; mismatch ⇒ error, nothing stored for that index |
| `finish-image-upload` | (session owner) | requires every index, verifies total size + whole-file `sha256`, validates the image, attaches to every slot (same payload as `upload-image`), clears the session |

Image guidance for clients: always pass `sha256`; use `fields` to attach one image to several slots at once; use the chunked tools when the base64 is longer than ~8,000 chars; after every upload confirm with the matching get-* tool by comparing the returned `sha256`. Base64 via tool arguments is the only upload channel — there is no HTTP endpoint and no URL fetching. Verified metadata of every stored image is persisted in `image_files` (keyed by storage path) and lazily backfilled on read for pre-existing files.

Server class: `app/Mcp/Servers/InjazatServer.php`; tools under `app/Mcp/Tools/` extending `InjazatTool` (auth, permission checks, structured responses). Record UUIDs are never accepted from input; slug conflicts fail validation.
