# Injazat Platform — Dashboard Rebuild Plan

Rebuild of the "إنجازات شباب عمان" (Omani Youth Achievements) domain from the legacy `backend/` app (Laravel 11 / Filament 3) into the fresh `dashboard/` app (Laravel 13 / Filament 5), guided by `design/DESIGN.md`.

**Decisions:**
- Phase scope: **admin panel only** (models, migrations, Filament resources, settings, seeders). Public frontend is a later phase.
- Bilingual content: `*_ar` / `*_en` columns + `HasLocalizedAttributes` trait (`$model->t('title')`, Arabic fallback). Do NOT use spatie/laravel-translatable (installed but unused).
- Fresh start: new migrations + seeders; no legacy data import.
- Roles: `SuperAdmin`, `Admin`, plus new `Editor`.

## Dashboard conventions (must follow)

- **Models**: `App\Traits\HasUuidWithlog` (UUID PK + spatie activitylog `logAll()`), `SoftDeletes`, per-model Shield-style Policy (`ViewAny:Model` PascalCase format).
- **Migrations**: `uuid('id')->primary()`, `uuid()` foreign keys (no `constrained()` on non-standard names unless matching), `timestamps()`, `softDeletes()`.
- **Filament 5 split-schema resources**:
  ```
  app/Filament/Resources/<Plural>/
  ├── <Model>Resource.php        # thin: delegates to Schemas/Tables
  ├── Pages/{List,Create,View,Edit}.php
  ├── Schemas/<Model>Form.php    # static configure(Schema $schema)
  ├── Schemas/<Model>Infolist.php
  └── Tables/<Models>Table.php   # static configure(Table $table)
  ```
  Reference: `app/Filament/Resources/Users/`. Enum icons (`Heroicon::OutlinedX`), `->native(false)` on selects/pickers, nav badge = record count, nav group via `__('common.nav_groups.*')`.
- **Labels**: every user-visible string via `__()`. Per-resource lang files `lang/{ar,en}/<resource>.php` with keys: `model_label`, `model_label_plural`, `form.{sections,fields,placeholders,helpers}`, `table.{columns}`, `filters`, `tabs`, `infolist.*`. Reuse `common.*` keys (see `lang/en/common.php`: nav_groups, actions, fields, booleans, translations…). Arabic is the primary UI language; both ar and en files must be complete.
- **Enums**: string-backed in `app/Enums`, implement Filament `HasLabel` (+ `HasColor`/`HasIcon` as needed), labels resolve through lang files. Selects consume via `->options(Enum::class)`.
- **Uploads**: `FileUpload::make('...')->image()->imageEditor()->directory(FileStoragePath::X->value)->visibility('public')`. Add new cases to `App\Enums\FileStoragePath`.
- **Settings**: spatie/laravel-settings; classes in `app/Settings`, migrations in `database/settings/`, Filament pages via `filament/spatie-laravel-settings-plugin` (installed).
- **Seeders**: orchestrated by `database/seeders/DatabaseSeeder.php`; Shield permissions regenerated in `PoliciesAndPermissions` seeder via `shield:generate --panel=dashboard`.
- Style: Pint; `declare(strict_types=1)` used in Enums/Policies (match neighboring files).

## Domain schema

All content models: `uuid` PK, `slug` (unique, nullable where slugless makes sense — required on public-facing records), `is_published` bool default false, `sort_order` int default 0, timestamps, softDeletes. Bilingual text via `_ar`/`_en` (en nullable).

| Model | Fields (beyond slug / is_published / sort_order) |
|---|---|
| `AchievementScope` | title_ar/en. Seeded: دولي (International) / محلي (Local) |
| `AchievementMainType` | title_ar/en, `icon_path`, `show_on_home` bool. hasMany SubAchievementType, Achievement, PublicFigure |
| `SubAchievementType` | FK `achievement_main_type_id`, title_ar/en |
| `Achievement` | FKs `achievement_scope_id`, `achievement_main_type_id`, `sub_achievement_type_id`; title_ar/en, `excerpt_ar/en` (text), description_ar/en (longText), `cover_image_path`, `video_url` nullable, `achieved_at` date nullable, `country_ar/en` nullable. belongsToMany PublicFigure (`achievement_public_figure`) + Organization (`achievement_organization`), UUID pivots with timestamps |
| `PublicFigure` | name_ar/en, `headline_ar/en`, description_ar/en (longText), story_ar/en (longText nullable), `image_path`, `tile_image_path`, `cutout_image_path`, `banner_image_path` (all nullable), FK `achievement_main_type_id` nullable, `is_featured` bool, `in_home_carousel` bool, linkedin_url / twitter_url / instagram_url / facebook_url / whatsapp_number / email / phone (nullable strings). belongsToMany Achievement |
| `OrganizationType` | title_ar/en. Seeded: حكومي (Government) / خاص (Private) |
| `Organization` | FK `organization_type_id`, name_ar/en, description_ar/en longText, `logo_path`, `logo_white_path` nullable, website_url nullable, manager_name_ar/en, manager_message_ar/en longText, `manager_image_path` nullable. belongsToMany Achievement; hasMany News |
| `News` | title_ar/en, excerpt_ar/en text, body_ar/en longText, `cover_image_path`, `published_at` timestamp nullable, FK `organization_id` nullable, `show_on_about` bool |
| `Testimonial` | person_name_ar/en, position_ar/en, quote_ar/en text, `photo_path` |
| `TeamMember` | name_ar/en, position_ar/en, `photo_path` |
| `ImageMedia` | title_ar/en, description_ar/en text nullable, `images` json (array cast), FK `public_figure_id` nullable, `is_active` bool default true |
| `VideoMedia` | title_ar/en, `url`, description_ar/en text nullable, `cover_image_path` nullable, FK `public_figure_id` nullable, `is_active` bool |
| `PublicationMedia` | title_ar/en, `file_path`, description_ar/en text nullable, `cover_image_path` nullable, `is_active` bool |
| `ContactUsMessage` | name, email, message text, `ip_address` nullable, `status` string default `new` cast to `ContactMessageStatus` enum. No slug/is_published/sort_order. |

**Dropped from legacy**: `Achiever` (half-finished duplicate), `LandingImage` (replaced by HomeSettings).

## Foundation code (step 1)

- `AppUserRole`: add `Editor = 'editor'`; include in `canAccessAdmin()`; lang keys `users.roles.editor` (ar/en).
- New `ContactMessageStatus` enum: `New`, `Read`, `InProgress`, `Completed` (values `new`, `read`, `in_progress`, `completed`); HasLabel/HasColor (new=info, read=gray, in_progress=warning, completed=success); labels via `contact.messages.status.*`.
- New trait `App\Traits\HasLocalizedAttributes`: `t(string $attribute, ?string $locale = null)` → `{attr}_{locale}`, fallback `{attr}_ar`.
- New trait `App\Traits\HasPublishing`: attribute scopes (`#[Scope]` style like `App\Traits\HasActiveColumn`) — `published()` (`where is_published = true`), `ordered()` (`orderBy sort_order`).
- Extend `FileStoragePath` enum: `AchievementCover = 'achievements/covers'`, `PublicFigureImage = 'public-figures/images'`, `PublicFigureTile = 'public-figures/tiles'`, `PublicFigureCutout = 'public-figures/cutouts'`, `PublicFigureBanner = 'public-figures/banners'`, `OrganizationLogo = 'organizations/logos'`, `OrganizationLogoWhite = 'organizations/logos-white'`, `OrganizationManagerImage = 'organizations/managers'`, `MainTypeIcon = 'achievement-types/icons'`, `NewsCover = 'news/covers'`, `TestimonialPhoto = 'testimonials/photos'`, `TeamMemberPhoto = 'team-members/photos'`, `MediaCover = 'media/covers'`, `MediaAlbum = 'media/albums'`, `SettingsImages = 'settings'`.

## Settings (step 3)

Classes in `app/Settings` + migrations in `database/settings/` (see `backend/database/settings/` for legacy field mapping):

- `GeneralSettings` (group `general`): logo, name_ar/en, address_ar/en, phone, phone_secondary nullable, email, social_media_accounts (array: platform, url), map_image, map_url
- `HomeSettings` (group `home`): hero_image, quote_ar/en, quote_author_ar/en
- `AboutSettings` (group `about`): intro_text_ar/en, intro_image_one..four, vision_ar/en, message_ar/en, values_ar/en, founder_message_ar/en, founder_name_ar/en, founder_position_ar/en, founder_photo, profile_document
- `MediaSettings` (group `media`): intro_text_ar/en nullable
- `ContactSettings` (group `contact`): phone_primary, phone_secondary, email, map_url, instagram_url, twitter_url, linkedin_url

## Filament resources (step 4)

Nav groups (add to `common.php` ar/en if missing): content (`content_management`), types/classifications, media, people, organizations, contact (`operations`), settings.

1. **Achievements**: `AchievementResource` (pivot multi-selects `public_figures` + `organizations` via `->relationship()`, cover FileUpload imageEditor aspect 473:283, `achieved_at` DatePicker `->native(false)`, video_url) + `AchievementScopeResource` + `AchievementMainTypeResource` (icon upload, `show_on_home` toggle) + `SubAchievementTypeResource` (main-type select).
2. **People**: `PublicFigureResource` (sections: identity AR/EN, images ×4 with design ratios — tile 351:427, cutout 848:477, — flags is_featured/in_home_carousel, social/contact section) + `TestimonialResource` + `TeamMemberResource`. All `->reorderable('sort_order')`.
3. **Organizations**: `OrganizationResource` (color + white logos ≤280:190, manager section) + `OrganizationTypeResource`.
4. **Media**: `NewsResource`, `ImageMediaResource` (multiple FileUpload → json images), `VideoMediaResource`, `PublicationMediaResource` (file upload). Media trio optional `public_figure_id` select.
5. **Contact**: `ContactUsMessageResource` — List + View pages only; `status` editable `SelectColumn` with `ContactMessageStatus` options; filters by status/date.

All resources: slug `TextInput` auto-generated from `title_ar`/`name_ar` (live onBlur, editable, unique-ish), `is_published` Toggle, `sort_order` numeric where ordered, View page, Table with toggleable date columns, `DeleteBulkAction`, striped, `paginated([10,25,50,100])`. Complete `lang/{ar,en}/<resource>.php` files.

## Settings pages (step 5)

Filament settings pages (plugin conventions) for General/Home/About/Media/Contact in nav group `settings`, labels via lang files (`settings.php` ar/en).

## Seeders (step 6)

- `RolesSeeder`: Editor case handled automatically via `AppUserRole::cases()`; after `shield:generate`, sync Editor role permissions: all content/media/people/organization/news resource permissions, NO User/Role/Settings permissions.
- `AchievementScopeSeeder` (دولي/International, محلي/Local), `OrganizationTypeSeeder` (حكومي/Government, خاص/Private), `AchievementMainTypeSeeder` (الرياضة/Sports, الثقافة/Culture, العلوم/Science, العسكرية/Military, الطبي/Medical, الإبتكارات/Innovation, الأدب/Literature, طلبة المدارس/Students, الفنون/Arts, ريادة الأعمال/Entrepreneurship — sort_order sequential).
- `UserSeeder`: add local `editor@admin.com` / `P@ssw0rd`.
- Register all in `DatabaseSeeder`.

## Cleanup (step 7)

- Remove unused `PrizmTheme` import in `DashboardPanelProvider`.
- Fix stale `account_id` reference in `CreateUser`.
- Fix `UserResource` `recordTitleAttribute = 'full_name'` / `getGloballySearchableAttributes()` referencing non-existent columns → use `name`, `email`, `phone_number`.

## Verification

`php artisan migrate:fresh --seed` green; `shield:generate` produces policies for all new resources; panel boots with all resources in nav; `vendor/bin/pint` clean; no hard-coded UI strings in resources.
