# Injazat: from static design to a dashboard-driven Laravel site

Status: proposal, 2026-11-10. Source of truth for the rebuild of `design/` as a Laravel 13 + Livewire 4 +
Filament 5 application where **every piece of content on the public site comes from the database and is
editable in `/admin`**.

## 1. What we learned from the design and from the last attempt

### 1.1 The design (`design/`, 41 HTML pages, 104 CSS, 68 JS, 443 assets)

* Arabic, RTL, one font (Cairo Variable; `about.html` also uses Tajawal). Design tokens live in `css/main.css`
  `:root` (`--primary #096770`, `--teal #0C7F89`, `--gold #A88344`, radii, type scale).
* **Not fluid.** Phone = a fixed 390 px design that the inline `vp-lock` script scales uniformly on every phone
  (it rewrites the viewport meta to `width=390`). Desktop = a separately designed layout in `css/desktop.css`
  (`min-width: 1100px`, a thin tablet band at 700–1099 px) plus per-page `*-desktop.css`. Phone-only overrides
  live in `phone-port-*.css` (`max-width: 699px`).
* Header, full-screen menu, footer and the `<head>` meta block are **copy-pasted into every page**.
  The desktop nav is built at runtime by `js/desktop.js` from the phone menu.
* Content comes from three places: hard-coded HTML (home, lists, profiles, achievement details, contact,
  inspiring), `window.*` data objects in JS files (`universities-data.js`, `federations-data.js`, `award-data.js`,
  `award-volunteer-data.js`, `bilarab-data.js`, `lab-data.js`, `about.html`'s `PAGE`, `about-roadmap.js`), and
  inline `<script type="application/json">` blocks (`#journey-data`, `#federations-data`, `#member-card-data`).
  Nothing is fetched from a URL. Pages choose a record with `?org=`, `?t=&id=`, `?id=`.
* Lists filter and paginate **client-side** over `data-*` attributes (`js/main.js applyFilter()`,
  `js/ach-filters.js`): category chips, gender, year range, scope (local/gulf/arab/international), sort, search
  (hamza/diacritics-normalised), page size 13/15 phone and 16 desktop.
* Linking between entities is done by **parsing other HTML pages at runtime** (`js/org-links.js` fetches
  `achievements.html` and `achievers.html` and reads `data-orgs`, `data-person`, `data-affil="squ|staff|role"`).
* A lot of content is flagged as placeholder: 34 `data-sample` duplicate cards, `news-content.json`
  (`fictional: true`), the magazine, org-news, the minister's and chairman's letters, team photos, hard-coded
  counters (221/2002/35, 33/10/6/2/15, 62/149, 8/12/5) that will drift.
* Three organisation templates (`institution.html`, `university.html`, `federation.html`) and three award pages
  (`award.html`, `award-volunteer.html`, `bilarab-award.html`) share most of their sections; the differences are
  type-specific sections and visual treatments, not different data.

The full per-page inventory (sections, fields, JS behaviour) is in §7.

### 1.2 Why the previous attempt (`~/projects/enjaz`, Codex) ended up "almost static"

| What it did | Why it hurts |
|---|---|
| One Blade file per HTML page (`v2/achievement-aly-alhbsy-01.blade.php` … `-10`), i.e. one view per **record** | Adding an achievement means adding a view; nothing is reusable |
| Pages auto-sliced into numbered blocks (`about-1.blade.php` … `about-10`) with `site_blocks.fields` JSON | Blocks are chunks of markup, not content types; the dashboard shows "field f0001" |
| `content_entries(kind, slug, data json)` as a universal table | No relations, no validation, no queries ("achievements of this university" = parse JSON in PHP) |
| `design_data json` columns bolted onto real tables | Two sources of truth per record |
| Design CSS/JS copied verbatim with `?v=` hand-versions and `<base href>` | No bundling, JS still rebuilds the DOM from JSON at runtime |

The good parts to keep: the taxonomy models (scopes, main types, sub types, organisation types), the
pivot tables `achievement_public_figure`, `achievement_organization`, `organization_public_figure(kind, role)`,
Filament resources per entity, Shield roles, settings pages, the `/cdn` proxy idea, the MCP server
(`app/Mcp`) and the chunked image-upload tools. Its database holds 19 achievers, 33 achievements, 57
organisations extracted from the design; our import (§5) regenerates that from `design/` directly.

## 2. Principles for the rebuild

1. **One table per real-world thing, with foreign keys.** Repeated shapes become shared polymorphic tables
   (media, sources, facts, milestones, quotes, social links). No `data json` catch-alls. JSON is allowed only
   for the *content of one rich field* (an article body made of blocks) and for small ordered lists of plain
   strings that are never queried (an achievement's "highlights").
2. **One Blade/Livewire component per design section.** `index.html` has 12 sections; each becomes a component
   that receives models, never markup. A page is a layout plus an ordered list of sections.
3. **Templates render records.** One `achievement.show` view renders all 12 achievement pages; one
   `organization.show` renders ministries, universities and federations (type-specific sections are
   `@if`/components); one `award.show` renders the three awards.
4. **Dashboard = Filament resource per table + settings pages**, with relation managers for children,
   drafts (`is_published`, `published_at`), soft deletes, activity log, roles (super_admin / admin / editor),
   and a "Content health" widget that lists placeholders and missing images.
5. **Server-side filtering and pagination (Livewire)** with the filter state in the URL (`#[Url]`), so the
   design's chips, popover filters, search and pager keep their look but work on the whole database and are
   shareable/indexable.
6. **Truly responsive.** Keep the design's CSS and its 699/700/1100 breakpoints, delete `vp-lock`, make the
   phone layout fluid between 360 and 699 px with `clamp()`/percentages, fill the tablet gap. Verify with
   `lab-shot ... --all` at 360/390/430/768/1024/1280/1536 before every merge.
7. **Assets through Vite.** Design CSS → `resources/css/site/**`, JS motion → `resources/js/site/**` as
   modules that (re)initialise on `livewire:navigated`. JS that *builds content from data* (`university.js`,
   `award.js`, `bha-final.js`, `lab-list.js`, `federations.js`, `org-links.js` …) is replaced by Blade.
8. **Images on OneBox** via spatie/laravel-medialibrary (collections per slot, queued conversions to webp
   in the sizes the design uses, focal point, alt, caption, credit). The site never serves from local disk.
9. **Arabic first, translatable by design.** Text columns use spatie/laravel-translatable JSON
   (`{"ar": "..."}`) so English can be switched on later without migrations (the old project's `_ar/_en`
   column pairs doubled every table). Decision needed, see §6.
10. **Placeholders become drafts.** Everything the design flags as fictional is imported unpublished so the
    editors see what to replace, and the public site never shows it.

## 3. Data model

Conventions: `id` bigint, `slug` unique, `is_published`, `published_at`, `sort_order`, `created_by`,
timestamps, soft deletes on content tables. `T` marks translatable JSON columns.

### 3.1 Taxonomies (small, editable lists)

| Table | Columns | Used by |
|---|---|---|
| `fields` (المجالات: الرياضة، الثقافة، العلوم، العسكرية، الطبي، الابتكارات، الأدب، طلبة المدارس، الفنون، ريادة الأعمال) | `name T, slug, icon (svg), description T, image, color, show_in_about_carousel, sort_order` | achievements, achievers, articles' categories, about page "fields" ring, chips on list pages |
| `scopes` (محلي، خليجي، عربي، دولي) | `name T, slug, label_short T, sort_order` | achievements, journey items |
| `organization_types` (حكومي، عسكري، خاص، أكاديمي، رياضي) | `name T, slug, css_class (t-gov…), sort_order` | organizations |
| `academic_groups` (جامعة، كلية، تقنية) and `sectors` (حكومية، خاصة، أهلية) | enum columns on `organizations`, not tables | |
| `news_categories` (علوم، همم بلا حدود، …) | `name T, slug` | news |
| `tags` | `name T, slug` | articles (pivot) |
| `contact_topics` | `label T, sort_order, is_active` | contact form radios |

### 3.2 Achievers (المنجزون)

`achievers`: `name T, name_en, slug, gender (m/f), headline T, story T (long), field_id, personal_quote T,
quote_context T, quote_source T, is_featured, is_inspiring, inspiring_tagline T, show_in_home_strip,
achiever_of_week (nullable date: the week it was featured), week_teaser T, portrait_trace_svg (nullable path
of the gold trace), published flags`.

Children:
* `achiever_journey_items`: `achiever_id, year, title T, icon (enum: book, cap, doc, feather, uni, trophy,
  star, plane, ball, medal, museum), sort_order` — the "مسيرتي" timeline, separate from achievements.
* `quotes` (polymorphic, see 3.9) — "قالوا عنه" cards: text, name, role, image.
* `social_links` (polymorphic) — platform, url, handle.
* `media` collections: `portrait`, `cutout` (hero), `avatar`, `tile`, `gallery` (3+ images with captions).
* Pivots: `achievement_achiever` (`is_primary`), `achiever_organization (kind: staff|alum|student, role T)`.

Related achievers = same `field_id`, excluding self (no table needed).

### 3.3 Achievements (الإنجازات)

`achievements`: `title T, slug, excerpt T, body T (rich, blocks), scope_id, field_id, achieved_at (date),
date_label T ("ديسمبر ٢٠١٦"), country T, highlights T (json list of 2–4 short strings), pull_quote T,
pull_quote_by T, pull_quote_role T, pull_quote_note T, is_featured (لوحة الشرف), show_on_home,
home_slot (feature|card|row, nullable), video_url, published flags`.

Children: `sources` (polymorphic), `media` collection `cover` (with focal point), pivots
`achievement_achiever`, `achievement_organization`. "Other achievements" = same primary achiever; the
career path (`#journey-data`) = all published achievements of the achiever ordered by `achieved_at`.

Derived numbers (never stored): total/international/local per achiever (`medal-stats`), site counters.

### 3.4 Organizations (المؤسسات)

`organizations`: `name T, name_en, abbr, short_name T, slug, organization_type_id, academic_group
(enum uni|college|tech, nullable), sector (enum gov|private|ahli, nullable), city T, hq T, founded (year),
declared (year), formerly T, tagline T, about T (long), website, color_primary, color_secondary,
logo_ratio (decimal, emblem vs plate), is_partner (home marquee), partner_group (enum gov|private|military|
sports), fields (pivot `organization_field` for the ministry chips), published flags`.

Children (shared polymorphic tables unless noted):
* `organization_stats`: `value T, label T, sort_order`.
* `organization_units`: `kind (enum college|unit|center|branch), name T, year, note T, sort_order`.
* `organization_leaders`: `kind (enum president|leader|board), title T, role T, name T, from_year, to_year,
  is_current, note T, sort_order` + `media` `photo` (cut-out).
* `letters` (polymorphic, 3.9): the president's/minister's word.
* `organization_rankings`: `year, rank, label T`; `organization_ranking_highlights`: `subject T, text T`.
* `organization_memberships`: `name T, year` (federations).
* `milestones` (polymorphic): `year, title T, place T`.
* `media` collections: `logo`, `logo_wide`, `emblem`, `cover`, `gallery` (photo|doc with `era`/`chapter`,
  year, title, note, credit), `videos` (youtube id, title, year, duration, official).
* `sources` (polymorphic).
* `news` with `organization_id` (3.7).
* Pivots to achievements and achievers (3.2/3.3).

"Same field" rail = same `organization_type_id`. Counts on `institutions.html` rings and the universities
map = `COUNT(*)` by type/group.

### 3.5 Awards (الجوائز)

`awards`: `name T, slug, kind (enum culture|volunteer|architecture|other: picks the visual theme),
motto T, about T, vision T, decree T, founded, launched, website, honor T, firsts T, color, published flags`.

Children:
* `award_goals`: `text T, sort_order`.
* `award_categories`: `kind (enum branch|category|prize|field), slug, name T, hint T, medal T, basis T,
  sort_order` (culture branches culture/arts/lit and prizes oman/arab; volunteer ind/org and its 15 fields).
* `facts` (polymorphic): `label T, value T, group (who|path|criteria)`.
* `letters` (polymorphic): chairman's word.
* `award_editions`: `award_id, number, year, type (enum oman|arab|null), title T, note T, status T, place T,
  area T, entries_count, finals_count, brief T, jury T, jury_all T, exec T, ceremony T, is_current,
  sort_order` + `media` `cover`, `gallery`.
* `award_edition_withheld`: `edition_id, award_category_id`.
* `award_winners`: `award_edition_id, award_category_id, rank (0 honorary, 1–3, null), name T, team T
  (json list of names), work T (work/project), why T, gender, place T, achiever_id (nullable link),
  organization_id (nullable), sort_order` + `media` `photo`, `logo`, `gallery`; `facts`, `sources`.
* `award_features`: `award_id, key (expo|ihya|...), title T, when T, where T, text T, note T, team T,
  sort_order` + `media` `image` — Bilarab's extra sections, generic enough for future awards.
* `sources`.

`awards.html` lists published awards with tallies computed from editions/winners.

### 3.6 Science lab (المختبر)

`lab_items`: `kind (enum invention|publication), slug, title T, short T, lead T, body T (blocks: h, p, ul,
q+by, img), field_id, year, date_label T, place T, who T, organization_id (nullable), medal (enum gold|
silver|bronze|null), medal_label T, event (ITEX|Geneva|…), event_label T, pub_type T, authors T, journal T,
finding_value, finding_text T, summary T, credit_text, credit_url, published flags` + `facts`, `sources`,
`media` `cover`, `gallery`.

### 3.7 News (الأخبار) and organisation news

`news`: `title T, slug, kind (enum news|achievement), news_category_id, organization_id (nullable),
tag T, summary T, dek T, body T (blocks), published_at, show_in_himam (همم بلا حدود), show_on_home,
published flags` + `facts`, `media` `cover` (with source caption), `gallery`.

### 3.8 Magazine (مجلة إنجازات)

* `writers`: `name T, slug, role T, bio T, category (field_id), is_featured (قلم العدد)` + `media` `photo`,
  `social_links`.
* `issues`: `number, published_at, title T, lead_article_id, quote T, quote_by T`.
* `articles`: `title T, slug, kicker T, dek T, body T (rich), field_id (tab), writer_id, issue_id,
  published_at, read_minutes (computed on save), is_featured, show_on_home, box_title T, box_items T (list),
  pull_quote T` + `media` `cover` (caption), pivot `article_tag`. Most-read = `views` counter.

### 3.9 Shared tables

| Table | Columns | Purpose |
|---|---|---|
| `media` (spatie) | model, collection, disk `s3`, path, sha256, size, mime, width, height, `custom_properties`: alt T, caption T, credit_text, credit_url, focal_x, focal_y, era/chapter, year, youtube_id | every image/video slot |
| `sources` | sourceable, title T, subtitle T, url, sort_order | credits and references |
| `facts` | factable, group, label T, value T, sort_order | key/value lists (story facts, award who/path, lab facts) |
| `milestones` | milestoneable, year, date, title T, text T, place T, image, sort_order | org milestones, the about roadmap (owner = `Site`) |
| `quotes` | quotable, type (official|person), text T, name T, role T, sort_order + media `avatar` | "قالوا عن", about testimonials |
| `letters` | letterable, title T, name T, role T, paragraphs T (list), source_text, source_url + media `photo` | leader's word on orgs and awards |
| `social_links` | linkable, platform, url, handle, sort_order | achievers, writers, site |
| `leader_quotes` | name T, title T, postscript T, lead T, body T, attribution T, sort_order, is_active + media `portrait` | home hero slides |
| `team_members` | name T, role T, sort_order + media `photo` | about |
| `menu_items` | location (header|footer_1|footer_2), label T, label_short T, url or route_name, sort_order, is_active | menu and footer columns |
| `contact_messages` | topic_id, name, email, phone, message, attachment (media), ip, handled_at | contact form |
| `redirects` | from_path, to_path, hits | `*.html` legacy URLs and slug changes |

### 3.10 Pages and page sections (the "configurable" layer without becoming a blob CMS)

* `pages`: `key (home|about|achievers|achievements|organizations|awards|lab|news|magazine|inspiring|contact),
  title T, meta_description T, og_image (media), hero_eyebrow T, hero_title T, hero_lead T, hero_cta_label T,
  hero_cta_url, noindex`. These are the fixed pages; their *structure* is code, their *texts* are data.
* `page_sections`: `page_id, key (one of the section components, e.g. home.hero, home.achievers,
  home.stats, home.latest, home.spotlight, home.awards, home.inspiring, home.articles, home.sports,
  home.culture, home.science, home.partners), is_enabled, sort_order, settings (json: small, validated per
  section: e.g. `limit`, `title`, `subtitle`, `field_id`)`. The dashboard shows the home page as a sortable
  list of sections with toggles. Unknown keys are refused.
* Settings (spatie/laravel-settings, one Filament page per group):
  `GeneralSettings` (site name T, tagline T, logo, favicon, og image, copyright, address T, phones, email,
  social handles), `HomeSettings` (stats: auto or manual values + labels, spotlight achiever_id),
  `AboutSettings` (hero facts, mission/vision/values T, founder name/role/paragraphs/photos),
  `ContactSettings` (channels, map image, intro), `SeoSettings` (defaults, JSON-LD organisation).

## 4. Application architecture

### 4.1 Front end

```
resources/views/
  layouts/site.blade.php            <head> meta from $page/$model + settings, header, menu, footer, @stack('site-css')
  components/site/                  Blade components = design sections (pure render, cacheable)
    header, menu, footer, hero/*, cards/* (achievement-card, person-card, org-card, award-card, article-row…)
    achievement/* (story-hero, facts, career-path, highlights, body, sources, share-sheet, acard)
    organization/* (hero, about, leaders, letter, units, rankings, milestones, gallery, people, sources)
    award/* (hero, about, word, editions-timeline, podiums, cycles, features, sources)
  livewire/site/                    Livewire 4 components where state is needed
    achievers-index, achievements-index, organizations-index, lab-index, magazine-index (filters, search, pager)
    organization-people, organization-achievements, award-winners (edition tabs), contact-form, site-search
resources/css/site/{tokens,base,header,footer,home/*,pages/*}.css   (design CSS, de-duplicated, fluid)
resources/js/site/{motion/*,share,idcard,acard,menu}.js             (design motion kept; re-init on livewire:navigated)
```

* Routes (`routes/web.php`): `/`, `/about`, `/inspiring`, `/contact`, `/achievers`, `/achievers/{slug}`,
  `/achievements`, `/achievements/{slug}`, `/organizations`, `/organizations/{slug}`,
  `/organizations/{slug}/people`, `/organizations/{slug}/achievements`, `/awards`, `/awards/{slug}`,
  `/lab`, `/lab/{kind}`, `/lab/{kind}/{slug}`, `/news`, `/news/{slug}`, `/magazine`, `/magazine/{slug}`,
  `/writers/{slug}`, `/search`, `/sitemap.xml`, `/cdn/{path}` (only if OneBox stays private), and a
  catch-all `/{legacy}.html` → `redirects`.
* Controllers are thin: load the model with its relations (`with([...])`), hand it to a view. Lists are
  Livewire components mounted by the route.
* Caching: full-page response cache (Redis tags per model type) for anonymous visitors, invalidated by
  model observers; menu/settings cached forever and flushed on save.
* SEO: title/description/OG per record, `og:locale ar_OM`, JSON-LD (`Person`, `Organization`, `Article`,
  `Event`), canonical URLs, sitemap from published records, `noindex` on drafts and previews.
* Preview: signed `/preview/{type}/{id}` routes render unpublished records for logged-in editors
  (Filament "Preview" action).
* Accessibility and performance: Cairo preloaded, images as `<picture>` webp with width/height, lazy
  loading below the fold, `prefers-reduced-motion` respected (already in the design CSS).

### 4.2 Dashboard (Filament 5, `/admin`)

* Navigation groups: المحتوى (achievers, achievements, organizations, awards, lab, news, magazine, writers),
  الصفحات (pages + sections, menu, leader quotes, team, roadmap, testimonials), الإعدادات (settings groups,
  taxonomies, contact topics, redirects), النظام (users, roles, activity log, contact messages, Horizon link).
* Each resource: form in tabs (الأساسية / النص / الصور / الروابط / SEO), relation managers for children,
  table filters by published/field/type, bulk publish, duplicate action, "Preview" and "Open on site" actions.
* Reusable Filament components: `TranslatableText`, `BlocksBuilder` (h, p, ul, q, img), `MediaSlot`
  (one collection, with focal point picker, alt, credit), `SourcesRepeater`, `FactsRepeater`.
* Dashboard widgets: counters (published vs drafts), "Content health" (placeholders, missing cover/alt,
  broken links), latest contact messages.
* Roles with Shield: `super_admin`, `admin` (all content + settings), `editor` (content, no publish of
  pages/settings) — adjustable.
* Activity log on every model; Horizon at `/horizon` for image conversions, mail, sitemap.

### 4.3 Packages to add to the template

`filament/spatie-laravel-media-library-plugin`, `spatie/laravel-medialibrary`, `spatie/laravel-translatable`
(+ Filament translatable plugin), `filament/spatie-laravel-settings-plugin`, `spatie/laravel-settings`,
`bezhansalleh/filament-shield`, `spatie/laravel-permission`, `spatie/laravel-activitylog`,
`spatie/laravel-responsecache`, `spatie/laravel-sitemap`, `laravel/mcp` (phase 7). All pinned to versions
released ≥ 2 weeks ago.

## 5. Importing the design content (one-off, repeatable)

`php artisan injazat:import-design {--only=…} {--fresh}` in `backend/app/Console/Commands/ImportDesign/*`:

1. **Data files → JSON.** A tiny Node script (`scripts/design-data-to-json.mjs`) evaluates each
   `window.*` data file in a sandbox (`vm`) and writes `storage/app/design-import/*.json`; the inline JSON
   blocks and `about.html`'s `PAGE` object are extracted the same way. Checked-in output so the PHP import is
   deterministic.
2. **HTML cards → records.** DOM parsing (symfony/dom-crawler) of `achievers.html`, `achievements.html`,
   `institutions.html`, `profile*.html`, `achievement-*.html`, `index.html`, `inspiring.html`,
   `articles.html`, `writers.html`, `news.html`, `org-news.html` into the tables above. `data-sample`
   cards and anything flagged تجريبي/تخيّلي are imported as **drafts**; conflicting facts (Gulf Cup 2009
   scope, Ali's counts) are logged to `storage/logs/import.log` for the editor.
3. **Images → OneBox** through medialibrary (sha256 de-duplication, alt from the design, credit from
   the data), with focal points taken from the design's `object-position`.
4. **Redirects**: every `design/*.html` (+ `?org=`, `?id=`) → the new URL.

The import runs on the demo copy first; the live copy is seeded once from a dump of the demo database
after review. The design folder stays in the repo as the visual reference.

## 6. Decisions to make before coding

| # | Question | Recommendation |
|---|---|---|
| 1 | Arabic only, or bilingual (AR/EN) from day one? | Arabic UI now, **translatable columns from day one**; add the English UI (`/en`, language switch, `dir="ltr"`) as a later phase. English text can be filled in the dashboard any time. |
| 2 | Drop the `vp-lock` scaling and make the phone layout fluid? | Yes. Scaling breaks text zoom, accessibility and Livewire positioning. Keep the design's 390 px as the reference width. |
| 3 | Image delivery: OneBox bucket `injazat` public-read with direct URLs, or private bucket behind `/cdn/{path}`? | Public-read bucket + direct URLs (fast, cacheable at Cloudflare). `/cdn` only for private attachments. |
| 4 | Can an achievement have several achievers (teams)? | Yes, pivot with `is_primary`; the design shows one author, the UI shows the primary and lists the rest. |
| 5 | URL language: English slugs (`/achievers/ali-alhabsi`) or Arabic? | English slugs, editable, with redirects on change. |
| 6 | Keep the MCP server (Claude connector "injazat") in the new project? | Yes, phase 7, same tools, ported to the new schema. |
| 7 | Magazine / writers / news: real sections at launch, or planned (hidden until content exists)? | Build them (they are small tables) but keep `page_sections` disabled until real content exists. |
| 8 | `about-journey-1.html` (saved variant) | Not implemented; it is an archived alternative. |
| 9 | Footer vs contact page disagree on phone/email | Single source: `GeneralSettings`. Confirm the real values. |

## 7. Appendix: design inventory (what each page needs)

**Home (`index.html`)** 12 sections: hero carousel of leader quotes (`leader_quotes`), achiever strip (achievers
`show_in_home_strip`), 3 gold stats (settings, auto-computed by default), latest achievements (1 feature +
2 cards + 2 rows, `show_on_home`/`home_slot`), spotlight person (`HomeSettings.spotlight_achiever_id`),
awards (2 squares + 1 banner from `awards`), inspiring (4 achievers `is_inspiring`), articles (1 lead +
2 minis), sports people (4 achievers of field الرياضة), culture people (4), science editorial (1 lead +
5 briefs from `lab_items`/`news`), partner marquees (organizations `is_partner` grouped), footer.
Motion to keep: `home-hero-majlis.js`, `main.js` strip rotation, `home-stats-gold.js` odometer,
`home-aisha.js`, `home-sports.js`, `home-upgrade.js` reveal, `scroll-cue.js`.

**Achievers list** hero wall (random tiles from achiever portraits), "منجز/منجزة الأسبوع" pair
(`achiever_of_week`, Oman outline SVG from `oman-outline.js`), field chips, federations panel when الرياضة
(organizations type رياضي), gender filter, search, 15/page. **Profile**: portrait with gold trace, socials,
story with read-more, medal stats (computed), journey (`achiever_journey_items`), achievements grid with
scope chips + year/type filters, "قالوا عن" (`quotes`), personal quote, 3-image gallery lightbox, related
(same field), ID card dialog (`profile-idcard.js` canvas from model data), share.

**Achievements list** hero wall (29 tiles), honor board (`is_featured`), chips, popover filters (gender,
year range, scope, sort), search, 13/16 per page with every 7th card wide. **Detail**: reading progress,
story hero (cover + focal point, scope label, read time), 3 facts (date_label, field, country), author
block (gender icon), career path from the achiever's achievements, 2–4 highlights, body, optional pull
quote, sources, 3 other achievements, share sheet, achievement card canvas (`acard`).

**Organizations** list: Sultan photo hero, letter dialog (`orgs-letter.js`), rings with counts by type,
featured, chips by type, search. Universities page: map hero (`universities-hero.js`), group chips, search.
Organization page (one template): hero (logo/emblem/plate by `logo_ratio`, stats, jump links), about,
leaders ribbon + carousel (president|leader|board), leader's word (`leader-word.js`), achievements
(own + members', type tabs المؤسسة/طلبة/أكاديميون, gender), units/colleges, rankings chart, milestones,
people (first 8 → `/people`), gallery (album+hall for academic, era grid for federations, `uni-library.js`,
`fed-gallery.js`), memberships (federations), fields chips + same-type rail (ministries), sources, news.

**Awards** list (`awards-hero-words.js`); award page by `kind`: culture = medal timeline of editions with
winner preview sheet (`award.js`), volunteer = podiums per edition + early editions counts
(`award-volunteer-*.js`), architecture = cycles with design images, expo/ihya features, radial "who"
(`bha-final.js`, `bha-hero-motion.js`). All: hero stats, about/goals, word, how/criteria/categories,
current edition, sources.

**Lab**: `science.html` hero with counters (computed), desktop showcase + periodic-table tiles
(`lab-showcase.js`), phone tabs inv/pubs (latest 6); `lab-all` with filters per kind; `lab-item` with
hero/medal/read time/facts/body/credit/sources/related (inventions) or finding/summary/copy-citation
(publications).

**News** list by category + `himam` strip; **org-news** detail (mast with organisation, facts, gallery,
share, more). **Magazine**: hero wall + search, mast (issue), lead, quote, tabs by field, rows, most read,
writers, CTA; article page with progress, byline ring, cover, lead/h2/pull/box, tags, share, author bio,
read-also; writers page with feature + grid + tabs.

**About**: hero with 3 facts, mission/vision/values, fields 3D ring (`fields` taxonomy), roadmap
(`milestones` on Site, 17 items, dialog, desktop centred line), founder (settings), testimonials orbit
(`quotes` on Site), team deck, our-achievements filterable row + lightbox (media on Site), media kit,
explore card. **Inspiring**: strip of 4 panels + photo wall, filter chips + tile wall, همم بلا حدود (news
`show_in_himam`), nominate CTA. **Contact**: intro, channels (settings), form (topics table, attachment,
real endpoint + mail + dashboard inbox), location.

Site-wide: menu (12 items), footer (5 + 7 links, socials, phone, email, address, copyright), OG defaults,
favicon `ornament.svg`, logo `logo.svg`, Cairo font, tokens.

## 8. Delivery plan (each phase = branch → PR → CI green → demo review with `lab-shot` → merge → `ghaklab-deploy`)

| Phase | Scope | Output |
|---|---|---|
| 0 Foundation | packages, media (OneBox), translatable, settings, roles, menu/footer/header components, layout, Vite pipeline for design CSS/JS, fluid base CSS without `vp-lock`, redirects, response cache | empty site with real header/footer; dashboard with settings + menu |
| 1 Achievers + achievements | taxonomies, achievers, achievements, lists (Livewire filters), profile, detail, share/ID card/acard JS | the core of the site, with imported real achievers |
| 2 Organizations | organizations + children, list, universities map, one organization template, people/achievements subpages, org news | institutions section complete |
| 3 Awards | awards, editions, winners, features, three visual kinds | awards section complete |
| 4 Lab, news, magazine | lab_items, news, writers, issues, articles | remaining content sections |
| 5 Composed pages | home sections + page builder, about, inspiring, contact (form + inbox + mail), search, sitemap, SEO | full site |
| 6 Import + QA + launch | full import from design, content-health pass, screenshots at all widths, performance (Lighthouse), accessibility, deploy to injazat.fahim.om, hand-over notes | live |
| 7 Later | MCP server port, English UI, newsletter/share counters | |

Estimate: phases 0–1 are the largest (they set every pattern); 2–5 reuse them.
