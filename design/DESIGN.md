# إنجاز (Injazat) — Design & Content Specification

This document describes the static HTML implementation in `design/` (built from the Adobe XD
prototype) so the pages can be turned into Laravel Blade views fed by the backend in `backend/`.

- **Source design:** Adobe XD "Flow 1" — <https://xd.adobe.com/view/625851de-9688-4dad-9ac6-b418816010dc-370b/>
- **Stack:** static HTML + Tailwind CSS (Play CDN) + Alpine.js 3 + `assets/css/style.css` + `assets/js/app.js`
- **Language / direction:** Arabic, `lang="ar" dir="rtl"` on every page
- **Preview:** run `python -m http.server` inside `design/` and open `index.html`

---

## 1. File structure

```
design/
├── index.html               الصفحة الرئيسية   (XD: Web 1920 – 6)
├── about.html               عنا               (XD: Web 1920 – 18)
├── achievers.html           المنجزين          (XD: Web 1920 – 12 / 13)
├── achiever-detail.html     ملف المنجز        (XD: Web 1920 – 11)
├── achievements.html        الإنجازات         (XD: Web 1920 – 9)
├── achievement-detail.html  تفاصيل الإنجاز    (XD: Web 1920 – 14)
├── institutions.html        المؤسسات          (XD: Web 1920 – 15)
├── institution-detail.html  تفاصيل المؤسسة    (XD: Web 1920 – 16)
├── contact.html             تواصل معنا        (XD: Web 1920 – 17)
├── media.html               الإعلام           (no XD screen – built from existing components)
├── DESIGN.md
└── assets/
    ├── css/style.css        design tokens + all shared components
    ├── js/app.js            Alpine components + scrollbar-width helper
    └── images/
        ├── brand/           logo-white.png, mark.svg, mountain-flag.svg
        ├── icons/           phone/email/location/social icons, stat icons, search
        │   └── cat/         category icons (sports, culture, science, …)
        ├── about/           founder.png, team-member.png, value icons (+ @2x)
        ├── hero/            featured-banner.jpg (+ @2x) – homepage featured achiever
        ├── logos/           institution logos
        └── photos/          all content photos (placeholders for CMS uploads)
```

Everything under `assets/images/photos` and `assets/images/logos` is **sample content** that the
backend will replace with uploaded files. Everything under `brand/`, `icons/` and `about/icon-*`
is part of the design and stays as static assets.

---

## 2. Layout & scale system

The XD artboards are **1920 px wide**. The CSS maps **1 rem = 10 design px**, so a value taken from
XD (e.g. `top: 387px`, `font-size: 33px`) is written as `3.87rem` / `3.3rem`.

```css
html { font-size: 7px; }                                   /* < 1024px (mobile/tablet) */
@media (min-width:1024px){ html { font-size: calc((100vw - var(--sbw)) / 192); } }
```

- On desktop the whole page scales with the viewport and always fills the full width
  (1920 px screen → 1:1 with XD; 2560 px screen → everything 1.33×).
- `--sbw` (scrollbar width) is set by `app.js` so the page fills the visible width exactly.
- Desktop sections use absolute positions copied from XD (`lg:absolute lg:top-[…] lg:right-[…]`).
  Below 1024 px those classes are off and content stacks in a single column.
- All pages are wrapped in `<div class="page">`.

When converting to Blade keep the `lg:` utility classes exactly as they are — they *are* the
pixel spec.

---

## 3. Design tokens

### Colours

| Token | Hex | Usage |
|---|---|---|
| `--teal` | `#096770` | headings, primary buttons, pagination arrows, active filter |
| teal light | `#0C7F89` | gradient start |
| `--teal-dark` | `#0E4348` | gradient end |
| teal deep | `#063135` / `#053439` | inner headers, footer end |
| `--leaf` | `#91C46C` | active nav pill |
| `--gold` | `#A88344` | article tags, contact submit button |
| quote gold | `#F4DBA8` | homepage hero caption |
| `--ink` | `#171717` | body text |
| `--muted` | `#7A797E` | dates, card descriptions |
| title dark | `#1B1E24` | card titles, active page number |
| tag bg | `#D4F8F8` | achievement card tags |
| filter text | `#525252` | filter pill labels |
| grey band | `#F7F7F7` | "المزيد" bands, media library band |
| quote card | `#EDEDED` | testimonial cards |

### Gradients

| Where | Value |
|---|---|
| Homepage hero | `linear-gradient(180deg, #0D6973, #B4CED0)` |
| Inner header (listing pages) | `linear-gradient(180deg, #096771, #053439)` |
| Profile / institution header | `linear-gradient(211deg, #0C7F89, #0E4348)` |
| Footer | `linear-gradient(205deg, #0B626A, #063135)` |
| "المزيد" button | `linear-gradient(269deg, #0C7F89, #0E4348)` |
| Person tile overlay | `linear-gradient(179deg, transparent, #DACAAF4B 72%, #A88344AF)` |

### Typography

- **Design font:** *Neo Sans Arabic* (licensed, not bundled).
- **Web fallback in use:** *Cairo* (Google Fonts) — closest free match in width and weight.
  `font-family: 'Neo Sans Arabic', 'Cairo', sans-serif;` — dropping licensed Neo Sans webfont
  files into `assets/fonts` with an `@font-face` will take over automatically.
- XD "Medium" is rendered as Cairo **700**; "Regular" as **400**; filter pills use **600**.

| Element | Size / line-height |
|---|---|
| Nav links | 20 / 28 |
| Page title (inner header) | 67 / 94 |
| Section title (`.section-title h2`) | 49 / 69, teal |
| Small section title ("المزيد") | 32 / 44, black |
| Homepage hero quote | 33 / 47 |
| Body / article text | 26 / 36 |
| Card title | 24 |
| Card description | 16 / 25 |
| Footer links | 16 / 22, letter-spacing 1.26 |

---

## 4. Shared components (`assets/css/style.css`)

| Component | Class | Spec (XD px) | Used on |
|---|---|---|---|
| Site header / nav | `.site-nav`, `.is-active` | 6 links, active pill 138×46 `#91C46C` r23 | all |
| Logo | `brand/logo-white.png` | 117 wide at (right 198, top 62) | all |
| Mobile menu | Alpine `siteNav` | hamburger + dropdown panel | all |
| Inner header | `.inner-header` | 1920×421, title + search | listings, media |
| Search box | `.search-box` | 627×65 `#F5F5F5` r16 | listings, media |
| Section title | `.section-title` + `mark.svg` | 49px teal + 96×60 brand mark | home, about, institution |
| "المزيد" button | `.btn-more` | 265×59 r40 gradient | home |
| Achievement card | `.ach-card` | 509×605, image 473×283, 2 tags, date, title, 3-line desc, "اقرأ المزيد" | home, achievements, profile, article |
| Person tile | `.tile` | 351×427 r45, category pill + name pill | home, achievers |
| Small person tile | `.tile-sm` | 266×323 r45 | profile "المزيد" |
| News card | `.news-card` | 381×436, image 345×208, date, title | institution, about, media |
| Filter pill | `.filter-pill`, `.is-active` | 168×83 r19, icon + label | listings, profile tabs |
| Pagination | `.pager` | 48px teal arrows, 26px numbers | achievers, profile, media |
| Media item | `.media-item` + `.media-btn` | h345 r32, 77px dark round icon | profile |
| Media library tile | `.lib-tile`, `.lib-profile` | 571/524/462 × 345, teal multiply overlay | about, media |
| Value card | `.value-card` | 487×640 r68 | about |
| Testimonial card | `.quote-card` | 878×480 `#EDEDED` r74, round photo 252 | about |
| Footer | `.site-footer` | 1920×241: logo, 2 link columns, contact, copyright | all except contact |

### Alpine components (`assets/js/app.js`)

| Name | Purpose |
|---|---|
| `siteNav` | mobile menu open/close |
| `photoCarousel(items)` | homepage 5-photo carousel (center enlarged), prev/next |
| `readMore` | expand/collapse story text on profile |
| `contactForm` | client-side form state + success message |
| inline `x-data` | filters, tabs, pagination, news carousels, testimonials — all hold their data inline as **sample data** that Blade should replace with server data |

---

## 5. Pages — structure and content

Every listed field is something the backend must supply. "Sample" = currently hard-coded.

### 5.1 `index.html` — Home

| # | Section | Content |
|---|---|---|
| 1 | Hero (1920×1080) | portrait image (photo + ring + swoosh), quote text, quote author caption |
| 2 | Photo carousel | 5+ achiever photos (link → achiever profile) |
| 3 | Stats card | count of **achievers**, **achievements**, **fields** (main types) |
| 4 | أحدث الانجازات | 3 latest achievements (card), "المزيد" → achievements |
| 5 | Featured achiever banner | background image, name, short bio (≈ 450 chars), "إقرأ المزيد" → profile |
| 6 | Category rows ×3 | per category: title + 4 achiever tiles (photo, category, name) + "المزيد" |
| 7 | Footer | site contact details |

### 5.2 `achievers.html` — Achievers listing

- Inner header: title "المنجزين" + search by name
- Category filter pills (11 + "الكل") each with an icon, plus "تصفية" (advanced filter) button
- Grid of person tiles: 4 per row (photo, category, name) → profile
- Pagination

### 5.3 `achiever-detail.html` — Achiever profile

| Section | Content |
|---|---|
| Header (621 tall) | cut-out portrait (transparent PNG), Arabic name, English name, one-line title/description |
| Social bar | LinkedIn, email, Instagram, Twitter (any subset) |
| قصتي | long story text, collapsed to 4 lines with "اقرأ المزيد" |
| Counts | number of international (دولي) and local (محلي) achievements |
| المكتبة المرئية | 3 media items (image/video), each with type icon |
| إنجازاتي | tabs الكل / دولي / محلي + "الترتيب" (sort), achievement cards grid, pagination |
| المزيد | 5 related achievers (small tiles) |

### 5.4 `achievements.html` — Achievements listing

- Inner header "الإنجازات" + search
- 6 category filter pills + "تصفية"
- Achievement cards, 3 per row

### 5.5 `achievement-detail.html` — Achievement article

| Section | Content |
|---|---|
| Header (854 tall) | full-bleed cover image with teal multiply overlay, title, achiever tile (photo + name) |
| Article card | gold tags: **year**, **field**, **country**; share button; long body (multiple paragraphs) |
| المزيد | 3 related achievement cards |

### 5.6 `institutions.html` — Institutions listing

- Inner header "المؤسسات" + search
- Filter pills: الكل / حكومي / خاص (organization types)
- Logo cards 353×316, 4 per row → institution page

### 5.7 `institution-detail.html` — Institution

| Section | Content |
|---|---|
| Header (650 tall) | white logo, type label ("مؤسسة حكومية"), name |
| About card | description (multiple paragraphs) |
| Manager quote | manager message, manager name, manager cut-out photo |
| انجازاتنا | carousel of the institution's achievements/news (image, date, title) |
| المزيد | 4 other institutions (logo cards) |

### 5.8 `about.html` — About

| Section | Content |
|---|---|
| Hero | intro text (3 paragraphs) + 4 collage photos |
| Values | الرؤية / الرسالة / القيم – text each (icons are static) |
| National message | long letter (3 paragraphs) + founder photo, name, position |
| قالوا عن إنجازات | testimonials carousel: photo, quote, person name + position |
| فريق العمل | team members: photo, name, position |
| انجازاتنا | news carousel (image, date, title) |
| المكتبة الإعلامية | profile document (PDF), recorded interviews (videos), 1st & 2nd publications, photo gallery |

### 5.9 `contact.html` — Contact (single screen, no footer)

- Form: الاسم, البريد الإلكتروني, الرسالة → submit "ارسال" (+ success state)
- Social accounts (Instagram, Twitter, LinkedIn)
- Map image (links to Google Maps)
- Phone numbers, email

### 5.10 `media.html` — Media (not in XD)

- Inner header "الإعلام" + search
- آخر الأخبار: news cards grid + pagination
- المكتبة الإعلامية: same tiles as the About page

---

## 6. Mapping to the backend (`backend/`)

> Sections 6–7 describe the backend **as it is today** (Laravel 11 / Filament 3). The target
> architecture for the real website (Laravel 13 / Filament 5) is in **section 10**, which
> supersedes them where they differ.

### 6.1 Existing models → design

| Design concept | Existing model / setting | Notes |
|---|---|---|
| Achiever (person) | `PublicFigure` | name_ar/en, description, image_path, story_ar/en, social links ✔ |
| Achievement | `Achievement` | title, description, cover_image_path, video_url ✔ |
| Local / international | `AchievementScope` | drives دولي / محلي tabs and profile counts ✔ |
| Category / field (الرياضة، الثقافة…) | `AchievementMainType` | drives filter pills, homepage rows, "عدد المجالات" ✔ |
| Sub-category (كرة القدم، الأدب…) | `SubAchievementType` | second tag on achievement card ✔ |
| Achievement ↔ achievers | `achievement_public_figures` pivot | profile "إنجازاتي", article achiever tile ✔ |
| Achievement ↔ institutions | `achievement_organizations` pivot | institution "انجازاتنا" ✔ |
| Institution | `Organization` | name, description, logo, manager name/message/image ✔ |
| Government / private | `OrganizationType` | institutions filter ✔ |
| Homepage hero | `LandingImage` | image, description (quote), subtitle (author) ✔ |
| Videos | `VideoMedia` | "لقاءات مسجلة", profile media library |
| Publications | `PublicationMedia` | "الإصدار الأول / الثاني" |
| Photo galleries | `ImageMedia` (json `images`) | "صور متنوعة" |
| Contact form | `ContactUsMessage` | name, email, message, ip, status ✔ |
| Site contact info, socials, map, profile PDF | `CompanySettings` | logo, about_us_document, map_image, address, phone, email, socialMediaAccounts ✔ |
| About page content | `TeamSettings` | introText, introImageOne–Four, vision, message, values, managerMessage/Name/Position, members ✔ |
| (unclear) | `Achiever` model | only name/english_name/title/profile_image and no relations — overlaps with `PublicFigure`; consider using it for team members or removing it |

### 6.2 Fields the design needs that don't exist yet

| Model | Missing field | Why (where it shows) |
|---|---|---|
| `PublicFigure` | `title_ar/en` (one-line headline) | profile header badge |
| `PublicFigure` | `cutout_image_path` (transparent PNG) | profile header portrait |
| `PublicFigure` | `tile_image_path` (3:4 portrait) | person tiles (or crop `image_path`) |
| `PublicFigure` | `is_featured` + `featured_banner_path` | homepage featured achiever |
| `PublicFigure` | `main_type_id` (primary field) | category pill on tiles, filtering achievers by category |
| `PublicFigure` | media relation (images/videos) | "المكتبة المرئية" on the profile |
| `Achievement` | `achieved_at` (date) | card date, year tag on article |
| `Achievement` | `country_ar/en` | article tag "سلطنة عمان" |
| `Achievement` | `short_description` (or truncate) | card 3-line excerpt |
| `AchievementMainType` | `icon_path`, `sort_order`, `show_on_home` | filter pill icons, homepage category rows |
| `Organization` | `logo_white_path` | white logo on the institution header |
| new `Testimonial` | photo, quote_ar/en, name, position, sort_order | about "قالوا عن إنجازات" |
| new `News` (or reuse achievements) | image, date, title, body, organization_id | "انجازاتنا" carousels, media page "آخر الأخبار" |
| `TeamSettings.members` | each item: name, position, photo | team grid (confirm the array shape matches) |
| `LandingImage` | `portrait_path` | hero portrait composite |
| homepage carousel | list of featured achiever photos | carousel under the hero (could be `is_featured` achievers) |

### 6.3 Computed values

| Value | Query |
|---|---|
| عدد المنجزين | `PublicFigure::count()` |
| عدد الانجازات | `Achievement::count()` |
| عدد المجالات | `AchievementMainType::count()` |
| Profile دولي / محلي counts | profile's achievements grouped by `achievement_scope_id` |
| Related achievers / achievements | same main type, excluding the current record, limit 5 / 3 |

---

## 7. Suggested routes

| Page | Route | View |
|---|---|---|
| Home | `/` | `pages.home` |
| About | `/about` | `pages.about` |
| Achievers | `/achievers?type={id}&q=` | `achievers.index` |
| Achiever profile | `/achievers/{publicFigure}` | `achievers.show` |
| Achievements | `/achievements?type={id}&scope={id}&q=` | `achievements.index` |
| Achievement | `/achievements/{achievement}` | `achievements.show` |
| Institutions | `/institutions?type={id}&q=` | `institutions.index` |
| Institution | `/institutions/{organization}` | `institutions.show` |
| Media | `/media` | `pages.media` |
| Contact | `/contact` (GET + POST) | `pages.contact` |

Blade partials to extract (identical markup on every page today): `partials/header-nav`
(pass the active route), `partials/footer`, `components/achievement-card`,
`components/person-tile`, `components/news-card`, `components/filter-pills`,
`components/pagination`, `components/section-title`.

---

## 8. Image specs for uploads

| Image | Ratio / size (design px) | Notes |
|---|---|---|
| Achievement cover | 473×283 (≈ 5:3) | also used full-bleed 1920×854 on the article page |
| Person tile | 351×427 (≈ 4:5) | portrait, subject centered |
| Profile cut-out | 848×477, transparent PNG | subject on the right, fades left |
| Institution logo | fits inside 280×190 on white | full-colour; white version for header |
| News image | 345×208 (≈ 5:3) | |
| Media library | 571/524/462 × 345 | covers get a teal overlay automatically |
| Testimonial photo | 252×252 | displayed as a circle |
| Team member | 333×359 | photo on teal rounded card (currently one composite PNG) |
| Homepage carousel | 230×315 sides, 288×397 center | |

Serve 2× versions (`srcset`) where available; the XD exports used here already include `@2x` files.

---

## 9. Known gaps / decisions

- **Font:** Cairo stands in for Neo Sans Arabic until licensed web fonts are provided.
- **Media page** has no XD screen — built from existing components.
- **Mobile layouts** are not in the XD; they are a single-column fallback built for this project.
- **Third testimonial photo** (سلطان بن محمد النعماني) is cut off in XD and could not be captured;
  the carousel reuses another portrait until real photos are uploaded.
- All names, texts, dates and photos are XD sample content — replace with database content.
- Links that have no destination yet use `href="#"` (media tiles, news cards, social links on the
  profile).

---

## 10. Converting the design into the website (Laravel 13 + Filament 5)

Goal: the public site looks exactly like the HTML in `design/`, but **every text, image, link and
list comes from the admin panel** — either from a Filament **Resource** (Eloquent model, for
anything that is a list) or from a **Spatie settings page** (for one-off page content). Content
is relational: a tile, card or photo on one page is a record that links to its own page
everywhere it appears.

### 10.1 Target stack and upgrade path

| Layer | Today (`backend/composer.json`) | Target |
|---|---|---|
| PHP | ^8.2 | ^8.3 (required by Laravel 13) |
| Laravel | ^11.9 | ^13 |
| Filament | ^3.2 | ^5 (Livewire 4, Tailwind v4) |
| Livewire | ^3.5 | ^4 (comes with Filament 5) |
| Settings | `filament/spatie-laravel-settings-plugin` ^3.2 | the v5 release of the same plugin |
| Frontend CSS | Tailwind Play CDN (design) | Tailwind v4 compiled by Vite |
| Alpine | CDN (design) | the copy bundled with Livewire — **do not load it twice** |

Upgrade one major version at a time and run the test suite between steps:

1. **Laravel 11 → 12 → 13** following the official upgrade guides (or Laravel Shift).
2. **Filament 3 → 4 → 5** using the official upgrade script (`filament/upgrade`) at each step.
   Resource forms and infolists become **schemas** (`form(Schema $schema)`), and layout
   components move to the `Filament\Schemas` namespace.
3. **Third-party plugins** — check that each has a Filament 5 release before upgrading, and
   replace any that don't:
   - `awcodes/filament-table-repeater` → use Filament's built-in table repeater.
   - `bezhansalleh/filament-shield`, `bezhansalleh/filament-language-switch`,
     `tomatophp/filament-users`, `joaopaulolndev/filament-edit-profile`,
     `statikbe/laravel-filament-chained-translation-manager` → upgrade to their v5-compatible
     versions, or remove them if there isn't one.
4. Move Tailwind to v4 (`@tailwindcss/vite`). The design's arbitrary classes
   (`lg:top-[38.7rem]`, `bg-white/[0.77]`, `[mask-composite:intersect]`) work unchanged. v4 prefers
   the important modifier as a suffix (`text-[3rem]!`), but the design's prefix form (`!text-[3rem]`)
   still compiles.

### 10.2 Content rules

1. **Nothing is hard-coded in Blade** except the fixed design assets (`brand/`, `icons/`,
   `about/icon-*`) and the fixed UI labels (nav items, "اقرأ المزيد", "المزيد"…). UI labels go
   in `lang/ar/site.php` (and `lang/en/site.php`) so they're translatable.
2. **Lists → models** managed by Filament Resources (achievers, achievements, institutions,
   news, testimonials, team, media…).
3. **Single page content → Spatie settings** managed by Filament settings pages (hero quote,
   about-page texts, contact details…).
4. **Relationships, not copies.** If an achiever appears on the homepage, the homepage reads
   it from the `achievers` table. Nothing is entered twice.
5. **Every record has a `slug`, `is_published` and `sort_order`.** Public queries always use
   the `published()` scope and `ordered()` scope.
6. **Bilingual columns** keep the existing `_ar` / `_en` convention. Add one accessor trait
   (`HasLocalizedAttributes`) so Blade uses `$model->t('title')`, which returns `title_ar` or
   `title_en` for the current locale (falling back to Arabic).

### 10.3 Final data model

Existing tables are kept and extended; `achievers` (the unused duplicate model) is dropped and
`public_figures` becomes the single "achiever" entity.

**Taxonomies**

| Table | Columns (new in **bold**) | Used for |
|---|---|---|
| `achievement_scopes` | title_ar/en, **slug** | دولي / محلي tabs + profile counts |
| `achievement_main_types` | title_ar/en, **slug, icon_path, sort_order, show_on_home** | category pills, homepage rows, "عدد المجالات" |
| `sub_achievement_types` | achievement_main_type_id, title_ar/en, **slug** | second tag on cards |
| `organization_types` | title_ar/en, **slug** | حكومي / خاص pills, institution header label |

**Core content**

| Table | Columns (new in **bold**) | Relations |
|---|---|---|
| `public_figures` (achievers) | name_ar/en, description_ar/en, story_ar/en, image_path, social links, email, phone, **slug, headline_ar/en, tile_image_path, cutout_image_path, banner_image_path, achievement_main_type_id, is_featured, in_home_carousel, is_published, sort_order** | belongsTo mainType; belongsToMany achievements; hasMany media items |
| `achievements` | scope_id, main_type_id, sub_type_id, title_ar/en, description_ar/en, cover_image_path, video_url, **slug, excerpt_ar/en, achieved_at, country_ar/en, is_published, published_at** | belongsToMany publicFigures; belongsToMany organizations |
| `organizations` | type_id, name_ar/en, description_ar/en, logo_path, website_url, manager_name/message/image, **slug, logo_white_path, is_published, sort_order** | belongsToMany achievements; hasMany news |
| **`news`** (new) | **organization_id (nullable), title_ar/en, body_ar/en, image_path, published_at, slug, show_on_about, is_published** | belongsTo organization |
| **`testimonials`** (new) | **name_ar/en, position_ar/en, quote_ar/en, photo_path, sort_order, is_published** | — |
| **`team_members`** (new) | **name_ar/en, position_ar/en, photo_path, sort_order, is_published** | — |
| `video_media` / `publication_media` / `image_media` | existing columns, **public_figure_id (nullable), slug, sort_order** | belongsTo publicFigure (profile "المكتبة المرئية") |
| `contact_us_messages` | name, email, message, ip_address, status | — |
| `landing_images` | — | **replaced by `HomeSettings`** (one hero, not a list) |

Pivot tables `achievement_public_figures` and `achievement_organizations` stay as they are.

### 10.4 Spatie settings classes

The existing `CompanySettings` and `TeamSettings` are split by page so each admin settings
page matches one page of the site. Write a settings migration that copies the current values
into the new properties.

| Settings class (group) | Properties | Filament page |
|---|---|---|
| `GeneralSettings` (`general`) | site_name_ar/en, logo_white, footer_copyright_ar/en, phone, phone_secondary, email, address_ar/en, social_accounts (repeater: platform, handle, url), map_image, map_url | الإعدادات العامة |
| `HomeSettings` (`home`) | hero_quote_ar/en, hero_author_ar/en, hero_portrait (image), latest_achievements_title_ar/en, latest_count (default 3), category_rows_count (default 3), tiles_per_row (default 4) | الصفحة الرئيسية |
| `AboutSettings` (`about`) | intro_ar/en (rich text), collage_images (4 images), vision_ar/en, mission_ar/en, values_ar/en, national_message_ar/en (rich text), founder_name_ar/en, founder_position_ar/en, founder_image, profile_document (PDF) | صفحة عنا |
| `MediaSettings` (`media`) | first_publication_id, second_publication_id, featured_video_id, featured_gallery_id (selects of existing media records) | المكتبة الإعلامية |
| `ContactSettings` (`contact`) | page_title_ar/en, success_message_ar/en, notify_email | تواصل معنا |

The featured achiever banner and homepage carousel are **not** settings: they come from the
`is_featured` and `in_home_carousel` flags on `public_figures`, so the banner always links to a
real profile.

### 10.5 Where each section gets its content

| Page / section | Source | Links to |
|---|---|---|
| **Header nav** (all pages) | `lang/ar/site.php` + `route()` | each page; active pill = `request()->routeIs()` |
| **Footer** (all pages) | `GeneralSettings` | pages, `tel:`, `mailto:` |
| Home › hero | `HomeSettings` hero_* | — |
| Home › photo carousel | `PublicFigure::published()->where('in_home_carousel', true)->ordered()` — `tile_image_path` | achiever profile |
| Home › stats | counts of published achievers, achievements, main types (cached) | — |
| Home › أحدث الانجازات | `Achievement::published()->latest('achieved_at')->take(latest_count)` | achievement page; tags link to filtered listing |
| Home › featured banner | `PublicFigure::published()->where('is_featured', true)->first()` — banner image, name, `Str::limit(description)` | achiever profile |
| Home › category rows | `AchievementMainType::where('show_on_home')->ordered()->take(n)` each with 4 published achievers of that type | tile → profile; "المزيد" → `/achievers?type={slug}` |
| Achievers › pills | `AchievementMainType::ordered()` + icon | `?type=` filter |
| Achievers › grid | `PublicFigure` filtered by type + search `q`, `paginate(12)` | profile |
| Profile › header / social / story | the `PublicFigure` record | social URLs |
| Profile › دولي / محلي counts | its achievements grouped by scope | — |
| Profile › المكتبة المرئية | its video/image media (`public_figure_id`) | video / album pages |
| Profile › إنجازاتي | its achievements, `?scope=` tab, `?sort=`, paginated | achievement page |
| Profile › المزيد | 5 published achievers with the same main type | profile |
| Achievements › pills + grid | main types + `Achievement` filtered by type / scope / `q`, paginated | achievement page |
| Achievement › header | cover image, title, first linked achiever | achiever profile |
| Achievement › tags | `achieved_at` year, main type, `country` | `/achievements?type=` |
| Achievement › المزيد | 3 achievements with the same main type | achievement page |
| Institutions › pills + grid | `OrganizationType` + `Organization` filtered | institution page |
| Institution › header / about / manager | the `Organization` record | website_url |
| Institution › انجازاتنا | its `news`, falling back to its linked achievements | news / achievement page |
| Institution › المزيد | 4 published organizations of the same type | institution page |
| About › all fixed text and photos | `AboutSettings` | profile document download |
| About › قالوا عن إنجازات | `Testimonial::published()->ordered()` | — |
| About › فريق العمل | `TeamMember::published()->ordered()` | — |
| About › انجازاتنا | `News::published()->where('show_on_about', true)->latest()` | news page |
| About / Media › المكتبة الإعلامية | `MediaSettings` selected records + `AboutSettings.profile_document` | publication / video / album pages |
| Media › آخر الأخبار | `News::published()->latest('published_at')->paginate(8)` | news page |
| Contact › details, socials, map | `GeneralSettings` | social URLs, `map_url` |
| Contact › form | Livewire component → `ContactUsMessage` + optional mail to `ContactSettings.notify_email` | — |

### 10.6 Filament 5 admin panel

Navigation groups and what each one manages:

| Group | Resources / pages | Key form fields | Notes |
|---|---|---|---|
| **المنجزين** | `PublicFigureResource` | name, headline, main type (select), story (rich editor), tile / cut-out / banner images, social links, flags (featured, carousel, published), sort | Relation managers: **Achievements** (attach existing), **Videos**, **Image albums** |
| **الإنجازات** | `AchievementResource` | title, excerpt, description, cover, scope / main type / sub type (sub type options depend on the chosen main type), achieved_at, country, achievers (multi-select), organizations (multi-select), published | Table filters by scope / type / year |
| | `AchievementMainTypeResource`, `SubAchievementTypeResource`, `AchievementScopeResource` | title, slug, icon, sort, show_on_home | reorderable tables |
| **المؤسسات** | `OrganizationResource`, `OrganizationTypeResource` | name, type, description, logo + white logo, manager fields, published | Relation managers: **Achievements**, **News** |
| **الإعلام** | `NewsResource`, `VideoMediaResource`, `PublicationMediaResource`, `ImageMediaResource` | title, body, image, date, organization, achiever, show_on_about | |
| **عنا** | `TestimonialResource`, `TeamMemberResource` | name, position, quote / photo, sort | reorderable |
| **صفحات الموقع** | Settings pages: General, Home, About, Media, Contact | see 10.4 | one page per settings class |
| **الرسائل** | `ContactUsMessageResource` | read-only + status | badge with the count of new messages |

Admin conventions:

- Every `FileUpload` uses `->image()->imageEditor()` with the **aspect ratio from section 8**
  (e.g. tile 351:427, cover 473:283, testimonial 1:1), so editors can't upload images that break
  the layout. Store files on the `public` disk (or S3 — `league/flysystem-aws-s3-v3` is
  already installed).
- `slug` is generated from the Arabic title and stays editable.
- Use `->reorderable('sort_order')` on every table that has a sort column.
- Arabic and English fields sit side by side in tabs (`العربية` / `English`).
- Keep `filament-shield` roles: **admin** (everything), **editor** (content only, no settings or
  users).

### 10.7 Frontend implementation

**Assets.** Move `design/assets` into the app:

| From | To |
|---|---|
| `design/assets/css/style.css` | `resources/css/site.css` (imported by `resources/css/app.css` after `@import "tailwindcss";`) |
| `design/assets/js/app.js` | `resources/js/site.js` — register the components on `document.addEventListener('alpine:init', …)` using the Alpine copy Livewire provides |
| `design/assets/images/brand`, `icons`, `about/icon-*` | `public/images/…` (static design assets) |
| `design/assets/images/photos`, `logos`, `hero`, `about/*.png` | **seed data only** (see 10.9) — copied to `storage` by the seeder |

**Layout and components** (`resources/views`):

```
layouts/site.blade.php              <html lang dir>, @vite, <div class="page">, @yield/slot
partials/nav.blade.php              logo + 6 links, active pill via routeIs()
partials/footer.blade.php           GeneralSettings
components/inner-header.blade.php   title, search box (props: title, searchPlaceholder)
components/section-title.blade.php  mark.svg + h2
components/achievement-card.blade.php  (Achievement $achievement)
components/person-tile.blade.php       (PublicFigure $figure, size: lg|sm)
components/news-card.blade.php         (News|Achievement $item)
components/org-card.blade.php          (Organization $organization)
components/filter-pills.blade.php      (items, active, param, icons)
components/media-tile.blade.php        (title, image, icon, url)
components/pagination.blade.php        Laravel paginator view matching .pager
```

Copy the markup from the HTML files and replace sample values with component props. **Keep every
`lg:` class exactly as written** — those are the XD measurements.

**Pages.** Keep the existing Livewire full-page components, so filters, search and pagination
update without reloading and keep their state in the URL query string:

| Design file | Route name / URL | Component |
|---|---|---|
| index.html | `home` `/` | `Pages\Home` |
| achievers.html | `achievers.index` `/achievers` | `Pages\Achievers` (`#[Url]` type, q, page) |
| achiever-detail.html | `achievers.show` `/achievers/{publicFigure:slug}` | `Pages\AchieverShow` (scope tab, sort, page) |
| achievements.html | `achievements.index` `/achievements` | `Pages\Achievements` (`#[Url]` type, scope, q) |
| achievement-detail.html | `achievements.show` `/achievements/{achievement:slug}` | `Pages\AchievementShow` |
| institutions.html | `institutions.index` `/institutions` | `Pages\Institutions` (`#[Url]` type, q) |
| institution-detail.html | `institutions.show` `/institutions/{organization:slug}` | `Pages\InstitutionShow` |
| about.html | `about` `/about` | `Pages\About` |
| media.html | `media` `/media` (+ existing `/media/videos`, `/media/publications`, `/media/images-albums`) | `Pages\Media` |
| contact.html | `contact` `/contact` | `Pages\Contact` (form with validation + rate limit) |
| — | `news.show` `/news/{news:slug}` | `Pages\NewsShow` (reuses the article layout) |

Carousels (home photo carousel, news carousels, testimonials) keep their Alpine logic; Blade
outputs the records as JSON: `x-data="photoCarousel(@js($items))"`.

**Performance.**
- Eager-load relations on every list (`with(['mainType', 'subType', 'publicFigures'])`) to avoid
  N+1 queries.
- Keep the existing `CacheResponse` middleware, and clear the response cache from a model
  observer on every save/delete of a public model and every settings save.
- Cache the homepage stats.
- Generate responsive sizes on upload so `srcset` (1× / 2×) is real.

### 10.8 Link map (every place one record points to another)

```
PublicFigure ──< achievement_public_figures >── Achievement ──< achievement_organizations >── Organization
     │  belongsTo                                     │ belongsTo                                     │ belongsTo
     ▼                                                ▼                                               ▼
AchievementMainType ◄── category pills / home rows    AchievementScope (دولي/محلي)                    OrganizationType
     ▲                                                SubAchievementType                              │ hasMany
     └────────── Achievement.main_type ───────────────┘                                               ▼
PublicFigure ──< VideoMedia / ImageMedia (public_figure_id)                                           News
```

- Person tile, carousel photo, featured banner, article achiever tile → `achievers.show`
- Achievement card, "اقرأ المزيد", institution/about news fallback → `achievements.show`
- Card tags and article tags → `achievements.index?type=…`
- Category "المزيد" → `achievers.index?type=…`
- Logo card → `institutions.show`
- News card → `news.show`
- Media tiles → their media pages / PDF download

### 10.9 Seeding the design content

Write `DesignContentSeeder` so a fresh install looks exactly like `design/`:

1. Copy the images from `design/assets/images/{photos,logos,hero,about}` to
   `storage/app/public/seed/…` and save the paths on the records.
2. Create the taxonomies (11 main types with icons from `icons/cat`, sub types, both scopes,
   both organization types).
3. Create the achievers, achievements, institutions, news, testimonials and team members using
   the texts already in the HTML files, and attach the relations shown in the design (for
   example, Dr. Jokha ↔ the Booker achievement ↔ the Ministry of Culture).
4. Fill every settings class with the design texts (hero quote, about intro, vision/mission/values,
   national message, contact details).

The seeder doubles as the reference for the content editors will enter later.

### 10.10 Step-by-step plan

| # | Phase | Done when |
|---|---|---|
| 1 | Upgrade Laravel 11→13, Filament 3→5, plugins (10.1) | admin panel loads, existing tests pass |
| 2 | Migrations for new columns/tables, drop `achievers`, settings migrations (10.3, 10.4) | `migrate:fresh` runs cleanly |
| 3 | Models: relations, scopes (`published`, `ordered`), slugs, `HasLocalizedAttributes` | unit tests for relations and scopes |
| 4 | Filament resources, relation managers, settings pages (10.6) | an editor can manage every field listed in 10.5 |
| 5 | Vite + Tailwind v4, move CSS/JS/static images (10.7) | design CSS compiles with no CDN |
| 6 | Layout, partials, Blade components | components render with seed data |
| 7 | Livewire pages, one design file at a time | each page matches its HTML at 1920 px |
| 8 | Seeder (10.9) | fresh install is identical to `design/` |
| 9 | Caching, observers, SEO (meta title/description per record, OpenGraph image = cover) | cache clears after editing content |
| 10 | Tests + QA | Pest feature test per route (200 + key content), filter/search tests, contact form test; visual check of every page against the design at 1920 px and on mobile |

### 10.11 Definition of done

- Every page in `design/` has a live route that renders the same layout.
- Editing any record or settings page in Filament changes the site after the cache refresh, with
  no code change.
- No text, image or link on a public page is hard-coded except fixed design assets and UI labels.
- Every tile, card, logo and photo links to its own record's page.
- Unpublished records never appear publicly; slugs give stable, readable URLs.
