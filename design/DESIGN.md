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
