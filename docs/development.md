# Development guide (how the site is built)

Read `architecture.md` first (data model, rules, phases). This file is the practical "how" for every phase.

## Golden rules (from architecture.md §6b)

1. **No design page is copied.** Views contain no names, dates, quotes, numbers or image paths of content.
   UI labels go in `lang/ar/site.php` (+ `lang/en/site.php`), content comes from the database.
2. Every repeated thing on the site is a record with a Filament resource. If an editor can't add one, it's a bug.
3. One design `<section>` = one Blade component (or Livewire component when it has state).
4. Responsive exactly as the design: same CSS, same breakpoints, `vp-lock` phone scaling kept.
5. Check with screenshots next to the design before every merge.

## Where things live

| What | Where |
|---|---|
| Design reference (never edited) | `/design` |
| Design data as JSON (from `scripts/design-data-to-json.mjs`) | `backend/database/data/design/` |
| Design CSS, fonts, icons (as in the design, same file names) | `backend/resources/site/css`, `resources/site/assets` |
| Site JS (menu, crumb, later: section motion) | `backend/resources/site/js` (entry `site.js`, modules in `core/`, later `sections/`) |
| Page shell (head, header, menu, footer) | `app/View/Components/Site/Layout.php`, `resources/views/components/site/*` |
| Page views | `resources/views/site/pages/<page>.blade.php` |
| Page sections | `resources/views/site/sections/<page>/<name>.blade.php` (key `<page>.<name>`) |
| Which pages/sections exist, stylesheets per page | `config/site.php` |
| Dashboard | `app/Filament` (resources, settings pages in `Pages/`, `Support/Translatable.php`) |
| Design import | `php artisan injazat:import-design`, steps in `app/Support/DesignImport/Steps` |

## The page shell

```blade
<x-site.layout :page="$page" styles="home" body-class="home" :crumb="['title' => ..., 'url' => ..., 'sub' => ...]">
  @include('site.partials.sections')   {{-- the page's enabled sections in the editors' order --}}
</x-site.layout>
```

- `styles` picks `config('site.styles.<key>')`; the layout loads `main, site-menu, <before>, desktop, <after>,
  phone-footer`, which is the design's order. Copy the list from the design page's `<link>` tags.
- Title = `<page or record title><separator><site name>`; description/OG image fall back to SEO settings.
- Named slots: `<x-slot:head>` (extra `<head>` tags, JSON-LD) and `<x-slot:scripts>` (page scripts).

## Adding a page section (phases 1–5)

1. Add the key to `config/site.pages.<page>.sections` (label in Arabic). `php artisan site:sync` (also run on
   every deploy) creates the row; editors order/toggle it in صفحات الموقع -> أقسام الصفحة.
2. Create `resources/views/site/sections/<page>/<name>.blade.php`. It receives `$section` (`$section->setting('limit', 6)`).
   Rebuild the design markup with the same classes, fed by models. Shared cards go in `components/site/cards/`.
3. If the design section has motion JS, port it to `resources/site/js/sections/<name>.js` as a module that
   finds its root (`document.querySelectorAll('[data-...]')`) and does nothing when absent; import it in `site.js`.
   JS that **builds content from data** is not ported; that content is rendered by Blade.
4. Lists with filters/search/pagination are Livewire components (`app/Livewire/Site`), state in the URL (`#[Url]`).

## Adding a content type (record)

- Migration with translatable JSON columns, `slug`, `is_published`, `published_at`, `sort_order`, soft deletes.
- Model: `HasTranslations`, `ClearsResponseCache`, `LogsContentActivity`, `InteractsWithMedia` (collections per
  image slot; files land in OneBox `media/<uuid>/`, publicly readable), factory.
- Filament resource: forms with `Translatable::text()/textarea()`, `SpatieMediaLibraryFileUpload`, relation managers
  for children; then `php artisan shield:generate --all --panel=admin --option=policies_and_permissions` and
  `php artisan site:sync` (adds the new permissions to admin/editor).
- Importer step in `app/Support/DesignImport/Steps` + registered in `ImportDesign::STEPS`; placeholders as drafts;
  redirects for the design's record pages (`profile.html` -> `/achievers/<slug>`).
- Sitemap: add published records in `SitemapController`.
- Tests: route 200 + drafts hidden + filters + dashboard pages render.

## Public pages built so far

| URL | View / component | Design |
|---|---|---|
| `/achievements` | `livewire/site/achievements-index` (`App\Livewire\Site\AchievementsIndex`) | achievements.html |
| `/achievements/{slug}` | `site/pages/achievement` + `components/site/achievement/*` | achievement*.html |
| `/achievers` | `livewire/site/achievers-index` (`AchieversIndex`) | achievers.html |
| `/achievers/{slug}` | `site/pages/achiever` | profile*.html |
| `/organizations` | `livewire/site/organizations-index` + `site/pages/organizations` (hero slides, letter) | institutions.html |
| `/universities` | `livewire/site/universities-index` + `site/pages/universities` | universities.html |
| `/organizations/{slug}` | `site/organizations/{institution,university,federation}` (the type's template) | institution / university / federation.html |
| `/organizations/{slug}/achievements` | institution: `institution-achievements`; others: `livewire/site/organization-achievements` | institution-achievements / uni-achievements.html |
| `/organizations/{slug}/people` | `livewire/site/organization-people` (universities, federations) | uni-people.html |
| `/news/{slug}` | `site/pages/news` | org-news.html |
| `/awards` | `site/pages/awards` (awards with a card style) | awards.html |
| `/awards/{slug}` | `site/awards/{culture,volunteer,architecture}` (the award's kind) | award / award-volunteer / bilarab-award.html |
| `/lab` | `site/lab/index` (showcase, publications table, phone tabs) | science.html |
| `/lab/inventions`, `/lab/publications` | `livewire/site/lab-index` (`LabIndex`: medal / exhibition / field / year, order) | lab-all.html |
| `/lab/{slug}` | `site/lab/show` (invention story or publication with citation) | lab-item.html |
| `/news?category={slug}` | `site/pages/news-index` (news of a category, read in full, `#news-{slug}` anchors) | news.html |
| `/magazine` | `site/magazine/index` + `livewire/site/magazine-index` (tabs; the hero search sends `mag-search`) | articles.html |
| `/magazine/{slug}` | `site/magazine/show` (POST `/magazine/{slug}/view` counts reads for «الأكثر قراءة») | article.html |
| `/writers` | `site/magazine/writers` | writers.html |

- Shared cards: `components/site/cards/achievement` (variants grid / row / profile) and `cards/person`.
- Design scripts that only animate or handle UI are ported almost verbatim to `resources/site/js/sections/design/`
  (only asset paths and data sources changed). Content they used to build from data is rendered by Blade.
- `resources/site/css-site/overrides.css` (loaded last) holds the few rules that make design CSS data-driven
  (e.g. scope badge label from `data-scope-label`). The design CSS itself is never edited.
- Livewire gotchas: put `wire:ignore` / `wire:ignore.self` on sections whose DOM or classes JS changes
  (hero walls), or a Livewire re-render will undo the JS. In Blade, never use the inline `@php(...)` form in a view
  that also has `@php ... @endphp` (the compiler mixes them up), and build JSON blobs in `@php` blocks then
  `{!! json_encode(..., JSON_HEX_TAG) !!}` (multi-line `@json` breaks).
- Big "library" widgets that are pure UI over a data set (university album + screening room, federation gallery) keep the
  ported design script; the server prints their data as JSON (`window.INJAZ_LIBRARY`, `window.INJAZ_FED_GALLERY`) from
  the organization's gallery media (custom properties: title, year, note, chapter/era, kind photo|doc, credit, credit_url) and videos.
- Awards: editions hold winners (`award_editions`, `award_winners`); branches/prizes/categories/fields are `award_categories`;
  goals/who/path/criteria are polymorphic `facts`; extra sections are `award_features`; small design texts live in
  `awards.extra` ({key: {ar, en}}, edited in the dashboard tab «نصوص التصميم»). Interactive parts (medal filter and
  preview, volunteer tabs and winner card) read a winners JSON printed by the view; all text is in the HTML.
- Fixed pages keep their small design texts in `pages.extra` ({key: {ar, en}}); the keys and their dashboard labels
  are listed per page in `config/site.php` (`labels`) and appear in the page form under «نصوص التصميم». Read them with
  `$page->label('key', default)`. Pages with the `cutout` feature have a transparent hero image slot.
- Lab items: `lab_items` (kind invention | publication); the card / filter fields are columns, the story is rich text,
  «بطاقة الابتكار» is polymorphic `facts` (group `facts`), sources are `sources`.
- News: category news (`news_category_id`) are read on `/news`; organization news keep `/news/{slug}`. `News::url()` picks.
- Magazine: `article_categories` (tabs), `writers`, `issues` (newest published one = current: mast, lead article,
  quote), `articles` (read minutes computed on save; the pull quote goes after the first paragraph of the first section,
  the summary box before the second heading, the last paragraph gets the ornament).
- A page under another menu section sets `request()->attributes->set('menu_section', '/universities')` (menu highlight).
- Lists filter on the server; search ignores hamza, ta marbuta, alif maqsura and diacritics (`ArabicSearch`), counts
  use the design's Arabic agreement (`ArabicCount`), dates the design's format (`ArabicDate`).

## Database rules learned the hard way

- Translatable columns are **`jsonb`** (`$table->jsonb(...)`), never `json`: PostgreSQL cannot sort or compare `json`,
  and the dashboard sorts/searches them.
- Tables: search and sort translatable columns with `TranslatableColumn::search('name')` / `::sort('name')`
  (they query `name->ar` / `name->en`). Relationship selects on a translatable title need
  `->getOptionLabelFromRecordUsing(fn ($record) => $record->name)` (otherwise the raw JSON shows).
- Tests run on **PostgreSQL** (`injazat_test` locally, a postgres:16 service in CI), never SQLite, so these bugs fail CI.
- Records with a slug use it as route key: dashboard URLs are `/admin/achievers/<slug>/edit`.
- Images in forms: `MediaSlot::make('cover', 'label')` (upload + focal point + alt); galleries: a relation manager
  extending `App\Filament\Support\RelationManagers\GalleryRelationManager` (captions, focal, credit, order).
- Lists in a record (highlights): a `Repeater` stored as a JSON array of `{key: {ar, en}}`, read with `Localized::get()`.

## Commands

```bash
cd backend
php artisan site:sync                       # roles/permissions, pages/sections, first super admin (idempotent)
php artisan injazat:import-design           # fill from /design (only empty values; --force overwrites; --only=menus)
node ../scripts/design-data-to-json.mjs     # after the design's JS data files change
npm run build                               # no hot reload through Flock/demo
php artisan test --compact && vendor/bin/pint --dirty
```

Visual check against the design (serve the design on a local port; lab-shot uses the host network):

```bash
(cd ../design && python3 -m http.server 8777 --bind 127.0.0.1) &
lab-shot http://127.0.0.1:8777/index.html --size 390x844 --full --out /tmp/shots/design
lab-shot https://injazat.test/ --size 390x844 --full --out /tmp/shots/new     # also 768x1024, 1280x800, 1536x864
LAB_BROWSE_PASSWORD=password lab-browse https://injazat.test --login /admin/login --user admin@example.com /admin/...
```

## Images and OneBox

- Media library disk `s3` (OneBox), every file under `media/` (`App\Support\Media\MediaPathGenerator`).
  Only `media/*` is publicly readable (no listing); private files (contact attachments) go elsewhere.
- Image URLs are direct: `https://onebox.fahim.om/<bucket>/media/...`, cached by Cloudflare. A Cloudflare response
  header rule adds `Access-Control-Allow-Origin: *` on those paths (needed by the dashboard previews and the
  canvas cards). Bucket CORS rules exist too but Cloudflare's cache does not vary by Origin.
- Dev: conversions run during upload (`QUEUE_CONVERSIONS_BY_DEFAULT=false`); live: Horizon.
- Tests: `MEDIA_DISK=media_test` (local) or `Storage::fake(config('media-library.disk_name'))`.

## Caching

- Public pages: full-response cache (`spatie/laravel-responsecache`) on live; off in dev (`RESPONSE_CACHE_ENABLED=false`).
  Models with `ClearsResponseCache` and saving settings clear it.
- Menus: cached raw rows (`MenuItem::grouped()`); Laravel 13 refuses to unserialize models from the cache, so
  cache arrays/ids, never models.
