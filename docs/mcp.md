# MCP server (AI assistants)

The site exposes an authenticated [MCP](https://modelcontextprotocol.io) server (`laravel/mcp`) so AI assistants
(claude.ai connectors, Claude Code, ...) can manage achievements and achievers on behalf of a dashboard user.
It is a port of the old Enjaz dashboard's server: **same URL, tool names, argument names, auth flow and env
names**, so a connector keeps its configuration when the apps are swapped (see "Swapping from the old app").

## Endpoint

`POST /mcp` (`backend/routes/ai.php`, loaded by the package). Middleware, in this order:

1. `auth:api`: Passport bearer token (guard `api` in `config/auth.php`). No token: 401 with a `WWW-Authenticate`
   header pointing at the OAuth metadata.
2. `EnsureMcpAccess`: 404 when disabled; 403 `ip_not_allowed` outside the allowlist; 403 `mcp_access_denied`
   unless the user has a dashboard role **and** `Use:Mcp`; 403 `personal_tokens_disabled` for a personal token when
   those are off. Checked on every request, so removing the permission or the roles cuts access at once.
3. `LogMcpUsage`: one `mcp_usages` row per JSON-RPC message (method, tool, arguments with strings cut to 2,000
   chars, error state and message, duration, IP, token id, client name). Pruned after 30 days (`model:prune`, daily).
4. `throttle:mcp`: 120 requests per minute per user (`AppServiceProvider`).

`config/mcp.php` allows the `https://claude.ai` and `https://claude.com` browser origins (`MCP_ALLOWED_ORIGINS`);
laravel/mcp 1.x rejects other `Origin` headers (requests without one, the usual server-to-server case, pass).
Tool errors are in English (the server switches the locale; the site itself is Arabic).

## Enabling

```env
ENJAZ_MCP_ENABLED=true          # default false: /mcp answers 404
ENJAZ_MCP_ALLOWED_IPS=          # optional comma-separated IPs/CIDRs; empty = any
ENJAZ_MCP_PERSONAL_TOKENS=true  # allow personal access tokens (default true)
PASSPORT_PRIVATE_KEY=           # optional, see below
PASSPORT_PUBLIC_KEY=
```

Settings: `config/injazat.php` (`injazat.mcp.*`). The production compose file passes all of these
(`docker/docker-compose.prod.yml`, values in the Portainer stack settings).

**Passport keys** sign the tokens. Either set `PASSPORT_PRIVATE_KEY` / `PASSPORT_PUBLIC_KEY` (PEM; a single line
with `\n` works), or leave them empty: the container entrypoint generates `storage/oauth-{private,public}.key` once
into the storage volume (mode 600) and never again. Regenerating keys invalidates every token and disconnects
every assistant. The entrypoint also creates the Passport "Personal Access" client when none exists.

## Who may connect

`Use:Mcp` is a Shield custom permission (`config/filament-shield.php`, tab «صلاحيات مخصصة» in the role form).
`RoleSync` (`php artisan site:sync`, every deploy) creates it and grants it to `super_admin` and `admin`, never to
`editor`. The users table has no "active" flag: a user without any dashboard role (`User::PANEL_ROLES`) is refused
even with the permission (`User::canUseMcp()`). Inside the tools, every action also checks the Shield permission
of the resource (`ViewAny:Achiever`, `Create:Achievement` ...).

## Authentication

- **OAuth 2.1** (claude.ai and other AI clients): `Mcp::oauthRoutes()` publishes
  `/.well-known/oauth-protected-resource`, `/.well-known/oauth-authorization-server` and dynamic client
  registration `POST /oauth/register`; Passport serves `/oauth/authorize` and `/oauth/token`. A guest on the consent
  screen signs in on the dashboard login first and comes back. The consent screen is
  `resources/views/oauth/authorize.blade.php` (Arabic, RTL; refuses users without `Use:Mcp`), registered in
  `PassportServiceProvider`. Access tokens live 1 hour, refresh tokens 30 days.
- **Personal access tokens** (scripts, debugging; `ENJAZ_MCP_PERSONAL_TOKENS`): in tinker,
  `User::firstWhere('email', '...')->createToken('mcp')->accessToken`, sent as `Authorization: Bearer ...`.
  They live 90 days.

Connect claude.ai: Settings → Connectors → Add custom connector → `https://<site>/mcp`.

## Tools

Records are found by slug (or numeric id where the old tools took an id). Writes return the record in the same shape
as the get-* tools; when an argument was adapted or not stored the response carries a `notes` list.

| Tool | Permission | Notes |
|---|---|---|
| `list-taxonomies` | `ViewAny:Achievement` or `ViewAny:Achiever` | `scopes` and `main_types` (id, slug, title_ar, title_en); each main type has `sub_types` (always `[]`) |
| `search-achievements` | `ViewAny:Achievement` | `q` (title ar/en ignoring hamza etc., or slug), `scope`, `main_type`, `published`, `limit` ≤ 100; `total`, `truncated` |
| `get-achievement` | `View:Achievement` | `identifier`; achiever / organization slugs, `images.cover` metadata |
| `create-achievement` | `Create:Achievement` | `title_ar` required; slug from the title when omitted; draft unless `is_published` |
| `update-achievement` | `Update:Achievement` | `slug` + only the fields to change; `new_slug`; `achievers` / `organizations` re-sync |
| `delete-achievement` | `Delete:Achievement` | soft delete |
| `search-achievers` | `ViewAny:Achiever` | `q` (name ar/en or slug), `main_type`, `published`, `limit` |
| `get-achiever` | `View:Achiever` | `identifier`; achievement slugs, image slots metadata |
| `create-achiever` | `Create:Achiever` | `name_ar` required; draft unless `is_published`; new optional `gender` |
| `update-achiever` | `Update:Achiever` | partial; `achievements` re-sync |
| `delete-achiever` | `Delete:Achiever` | soft delete |
| `upload-image` | `Update:Achievement` / `Update:Achiever` / `Update:Organization` by `type` | base64 (`data:` prefix and whitespace fine), `field` or `fields`, optional `sha256` checked before anything is stored, 10 MB, jpeg/png/webp/gif, replaces the previous image |
| `begin-image-upload` | same, by `type` | `fields`, whole-file `sha256`, `total_bytes`, `chunk_count`; returns `upload_id` (30 min, bound to the user: cache key `mcp-image-upload:{userId}:{uploadId}`) |
| `append-image-chunk` | session owner | `upload_id`, `index`, `data`, `chunk_sha256` (mismatch: nothing stored for that index) |
| `finish-image-upload` | session owner | checks every chunk, total size and sha256, then attaches like `upload-image`; the session is kept on failure |

Images go into the media library (`config('media-library.disk_name')`, OneBox in production) with
`sha256`, `width`, `height` in the media's custom properties. The get-* tools return per slot
`{url, sha256, bytes, width, height, mime}` or `null`; images uploaded in the dashboard get their sha256 and size
computed once on first read. Code: `app/Mcp/Servers/InjazatServer.php`, `app/Mcp/Tools/*` (base `InjazatTool`),
`app/Mcp/ImageUploader.php`, `app/Mcp/ImageUploadSession.php`. Tests: `tests/Feature/Mcp`.

## Old → new mapping

### Taxonomies

| Old | New |
|---|---|
| scope (`achievement_scopes`) | `App\Models\Scope` (`scopes.slug`, title = `name`) |
| main type (`achievement_main_types`) | `App\Models\Field` (المجالات, `fields.slug`, title = `name`) |
| sub type (`sub_achievement_types`) | none: `sub_type` is accepted, **not stored**, reported in `notes`, always `null` in get-achievement. The specialty text is the new `discipline_ar` / `discipline_en` (Achievement `discipline`) |

### Achievement

| Old argument | New column |
|---|---|
| `title_ar` / `title_en` | `title` {ar, en} |
| `excerpt_*` | `excerpt` |
| `description_*` | `body` (rich text; plain text becomes `<p>` paragraphs, blank lines split them; HTML kept) |
| `country_*` | `country` |
| `scope`, `main_type` | `scope_id`, `field_id` |
| `achieved_at`, `video_url`, `is_published`, `sort_order` | same columns |
| `achievers` (PublicFigure slugs) | `achievement_achiever` pivot; the first slug is the primary achiever |
| `organizations` | `achievement_organization` pivot |
| new: `discipline_ar` / `discipline_en` | `discipline` |

### Achiever (old PublicFigure)

| Old argument | New |
|---|---|
| `name_*`, `headline_*`, `story_*` | `name`, `headline`, `story` |
| `description_*` | `short_bio` (the ID card bio) |
| `main_type` | `field_id` |
| `is_featured` | `is_featured` |
| `in_home_carousel` | `show_in_home_strip` |
| `is_published`, `sort_order` | same |
| `linkedin_url`, `twitter_url`, `instagram_url`, `facebook_url` | `social_links` rows, platforms linkedin, x, instagram, facebook (null or "" removes) |
| `whatsapp_number` | `social_links` platform whatsapp: handle = the number, url = `https://wa.me/<digits>` |
| `email`, `phone` | none: accepted, **not stored**, reported in `notes`, `null` in get-achiever |
| `achievements` | `achievement_achiever` pivot (a new link is primary only when the achievement had no achiever) |
| new: `gender` (`m`/`f`) | `gender` (required column; defaults to `m` on create, with a note) |

### Image slots

| Type | Old slot | Media collection |
|---|---|---|
| achievement | `cover` | `cover` |
| achiever | `image` | `portrait` (cards, profile) |
| achiever | `tile` | `avatar` (small round image) |
| achiever | `cutout` | `cutout` (profile hero) |
| achiever | `banner` | `week` («منجز الأسبوع» card; there is no wide banner on the new site) |
| organization | `logo` | `logo` |
| organization | `logo_white` | `logo_white` |
| organization | `manager_image` | `photo` of the organization's current (else first) leader (`organization_leaders`); the organization's `cover` when it has no leader |

## Swapping from the old app

- Set the same `ENJAZ_MCP_*` values in the Portainer stack settings and keep the same public URL; `/mcp` and the OAuth
  routes have the same paths.
- Tokens and OAuth clients live in the old app's database and its user ids are UUIDs, while this app's are integers.
  They are **not** carried over, so every assistant has to sign in again once (claude.ai: the connector asks to
  reconnect, or Disconnect / Connect). Copying the old Passport keys alone does not keep old tokens valid.
- Give `Use:Mcp` to the people who used the old connector (admins have it by default).
