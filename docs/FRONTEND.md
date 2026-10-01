# Frontend — Injazat public site (implemented in `dashboard/`)

Public site per `design/DESIGN.md` §10.7, built with Livewire 4 full-page components + Blade on the `layouts.site` layout. RTL Arabic-first (`ar` default locale, `en` mirror in `lang/{ar,en}/site.php`). Vite entry `resources/css/app.css` + `resources/js/app.js` (Tailwind v4 `@theme` tokens + design `site.css` + Alpine components via Livewire's Alpine — no CDN).

## Routes → components (`app/Livewire/Pages/`)

| Route | Component |
|---|---|
| `/` | `Home` (hero from HomeSettings, carousel, cached stats, latest achievements, featured banner, category rows) |
| `/about` | `About` (AboutSettings, testimonials, team, media library) |
| `/achievers`, `/achievers/{slug}` | `Achievers` (`#[Url]` q/type), `AchieverShow` (scope tabs, sort, related) |
| `/achievements`, `/achievements/{slug}` | `Achievements` (q/type/scope), `AchievementShow` (article, share, OG) |
| `/institutions`, `/institutions/{slug}` | `Institutions`, `InstitutionShow` (news carousel w/ achievement fallback) |
| `/media` + `/media/{videos,publications,images-albums,images-albums/{slug}}` | `Media`, `MediaVideos`, `MediaPublications`, `MediaImageAlbums`, `MediaImageAlbumShow` |
| `/news/{slug}` | `NewsShow` (article layout reuse) |
| `/contact` | `Contact` (validation, rate limit 5/min, stores ContactUsMessage, no footer) |

Shared Blade components in `resources/views/components/`: `achievement-card`, `person-tile`, `news-card`, `org-card`, `filter-pills`, `inner-header`, `pagination`, `section-title`, `media-tile`, `media-library`. All null-image guarded; all UI strings via `__('site.*')`.

Content: `DesignContentSeeder` (registered last in `DatabaseSeeder`, idempotent on slug) reproduces the design's sample content — 15 figures, 11 achievements (+pivots), 4 orgs, 8 news, 3 testimonials, 8 team members, media records, all settings classes; copies 55 design images to `storage/app/public/seed/`. **Requires `php artisan storage:link`.**

## Gotchas (conventions discovered during the build)

- `#[Scope]` trait scopes (`published()`, `ordered()`, `active()`) are `protected` — call statically (`Model::published()`) or via `Model::query()->published()`, both work; never add `public` to them.
- Pivot tables `achievement_public_figure` / `achievement_organization` use custom pivot models (`app/Models/Pivots/`) that auto-generate UUID ids — `attach()`/`sync()` work normally, but under `WithoutModelEvents` (seeders) pass explicit `['id' => (string) Str::uuid()]` in pivot attributes.
- Blade: splitting Arabic text with `preg_split('/\R/')` corrupts `م` — use `/\r\n|\r|\n/`.
- Footer suppression on contact page: an empty `@section('footer')` does not suppress — the view places an HTML comment inside the section.
- Livewire 4 full-page components use `#[Layout('layouts.site')]`; `@section('title'/'meta')` in component views feed the layout yields.

## Known gaps / deliberate deviations

- **"تصفية" (advanced filter) buttons** on listings render per design but are inert — XD defines no behavior.
- **`video_url`** on achievements and **`website_url`** on organizations have admin fields but no design slot on the frontend (not rendered).
- **`facebook_url` / `whatsapp_number` / `phone`** on PublicFigure unused on profile (design shows only LinkedIn/email/Instagram/Twitter).
- **No 2× `srcset`** — single upload field per image; design's @2x pipeline not implemented.
- Card dates render Latin digits (`j M Y`); design shows Arabic-Indic on some pages.
- **Page caching/SEO sweep (DESIGN.md phase 9)** not yet done: no `CacheResponse` middleware port, no observer-based cache clearing, no responsive image generation on upload.
- `filter-pills` lacks per-item class forwarding; `pagination` margin is fixed; `inner-header` GET form drops sibling query params (e.g. `type`) on submit — minor fidelity items.
- `MediaSettings.intro_text_*` exists but the media design has no intro block (unused).
