<?php

namespace App\Mcp\Tools;

use App\Mcp\ImageUploader;
use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\Field;
use App\Models\Organization;
use App\Models\Scope;
use App\Models\SocialLink;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use RuntimeException;
use Spatie\MediaLibrary\HasMedia;

/**
 * Shared authorization, lookup, mapping and output helpers of the Injazat content tools.
 *
 * The tools keep the old Enjaz dashboard's argument names (`title_ar`, `main_type`, `in_home_carousel` ...) and map
 * them onto this app's schema: `*_ar` / `*_en` pairs are the translations of one jsonb column, `scope` is a Scope,
 * `main_type` is a Field (docs/mcp.md has the full mapping).
 */
abstract class InjazatTool extends Tool
{
    /** Old achiever social fields => SocialLink platform. */
    protected const SOCIAL_FIELDS = [
        'linkedin_url' => 'linkedin',
        'twitter_url' => 'x',
        'instagram_url' => 'instagram',
        'facebook_url' => 'facebook',
        'whatsapp_number' => 'whatsapp',
    ];

    protected function user(): User
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            throw new RuntimeException('MCP tools require an authenticated user.');
        }

        return $user;
    }

    protected function can(string $ability): bool
    {
        return $this->user()->can($ability);
    }

    protected function limit(Request $request, int $default, int $max): int
    {
        return min($max, max(1, $request->integer('limit', $default)));
    }

    /**
     * @param  array<string, mixed>  $content
     * @param  list<string>  $notes  what was adapted or ignored (arguments without a place in this app)
     */
    protected function structured(array $content, array $notes = []): ResponseFactory
    {
        return Response::structured($notes === [] ? $content : $content + ['notes' => $notes]);
    }

    protected function findAchievement(string $identifier): ?Achievement
    {
        return $this->find(Achievement::class, $identifier);
    }

    protected function findAchiever(string $identifier): ?Achiever
    {
        return $this->find(Achiever::class, $identifier);
    }

    protected function findOrganization(string $identifier): ?Organization
    {
        return $this->find(Organization::class, $identifier);
    }

    /** Resolve the record an image upload targets (type is validated upstream). */
    protected function findImageTarget(string $type, string $identifier): (Model&HasMedia)|null
    {
        return match ($type) {
            'achievement' => $this->findAchievement($identifier),
            'achiever' => $this->findAchiever($identifier),
            'organization' => $this->findOrganization($identifier),
        };
    }

    /**
     * A record by slug or numeric id (soft-deleted records stay hidden).
     *
     * @template TModel of Model
     *
     * @param  class-string<TModel>  $model
     * @return TModel|null
     */
    private function find(string $model, string $identifier): ?Model
    {
        return $model::query()
            ->where('slug', $identifier)
            ->when(ctype_digit($identifier), fn ($query) => $query->orWhere('id', (int) $identifier))
            ->first();
    }

    /** Validation rule: the slug of a record that is not soft-deleted. */
    protected function liveSlug(string $table): Exists
    {
        return Rule::exists($table, 'slug')->whereNull('deleted_at');
    }

    protected function uniqueSlug(string $table, ?Model $ignore = null): Unique
    {
        return Rule::unique($table, 'slug')->ignore($ignore?->getKey());
    }

    /**
     * Id of a taxonomy (Scope or Field) by slug; existence is validated by the tool.
     *
     * @param  class-string<Model>  $model
     */
    protected function taxonomyId(string $model, ?string $slug): ?int
    {
        return $slug === null ? null : $model::query()->where('slug', $slug)->value('id');
    }

    /**
     * Slug from the request, or derived from the title with a uniqid fallback (Arabic titles may transliterate to
     * nothing). Provided slugs are unique-validated by the tool; generated ones get a suffix on collision.
     *
     * @param  class-string<Model>  $model
     */
    protected function newSlug(?string $slug, ?string $titleEn, string $titleAr, string $model): string
    {
        if (is_string($slug) && $slug !== '') {
            return $slug;
        }

        $base = Str::slug((string) $titleEn) ?: Str::slug($titleAr) ?: 'entry';
        $candidate = $base;

        while ($model::withTrashed()->where('slug', $candidate)->exists()) {
            $candidate = $base.'-'.uniqid();
        }

        return $candidate;
    }

    /**
     * Translations {ar, en} of one column from the `<prefix>_ar` / `<prefix>_en` arguments (empty ones left out).
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, string>
     */
    protected function translations(array $validated, string $prefix, bool $richText = false): array
    {
        $values = [];

        foreach (['ar', 'en'] as $locale) {
            $value = $validated["{$prefix}_{$locale}"] ?? null;

            if (is_string($value) && trim($value) !== '') {
                $values[$locale] = $richText ? $this->richText($value) : $value;
            }
        }

        return $values;
    }

    /**
     * Partial update of translatable columns: only the `<prefix>_<locale>` keys present are changed; null or ''
     * removes that translation.
     *
     * @param  array<string, mixed>  $validated
     * @param  array<string, string>  $map  column => argument prefix
     * @param  list<string>  $richText  columns holding HTML
     */
    protected function applyTranslations(Model $model, array $validated, array $map, array $richText = []): void
    {
        foreach ($map as $column => $prefix) {
            foreach (['ar', 'en'] as $locale) {
                $key = "{$prefix}_{$locale}";

                if (! array_key_exists($key, $validated)) {
                    continue;
                }

                $value = $validated[$key];

                if (! is_string($value) || trim($value) === '') {
                    $model->forgetTranslation($column, $locale);
                } else {
                    $model->setTranslation($column, $locale, in_array($column, $richText, true) ? $this->richText($value) : $value);
                }
            }
        }
    }

    /** Plain text (the old dashboard's descriptions) becomes paragraphs; HTML is kept as given. */
    protected function richText(string $value): string
    {
        if ($value !== strip_tags($value)) {
            return $value;
        }

        return collect(preg_split('/\R{2,}/u', trim($value)))
            ->map(fn (string $paragraph) => '<p>'.nl2br(e(trim($paragraph)), false).'</p>')
            ->implode('');
    }

    protected function translation(Model $model, string $column, string $locale): ?string
    {
        $value = $model->getTranslations($column)[$locale] ?? null;

        return is_string($value) && $value !== '' ? $value : null;
    }

    /**
     * Ids of live records by slug, in the order given.
     *
     * @param  list<string>  $slugs
     * @param  class-string<Model>  $model
     * @return list<int>
     */
    protected function idsBySlug(array $slugs, string $model): array
    {
        $ids = $model::query()->whereIn('slug', $slugs)->pluck('id', 'slug');

        return collect(array_values(array_unique($slugs)))->map(fn (string $slug) => $ids[$slug] ?? null)->filter()->values()->all();
    }

    /**
     * Link achievers to an achievement in the order given: the first is the primary achiever.
     *
     * @param  list<string>  $slugs
     */
    protected function syncAchievers(Achievement $achievement, array $slugs): void
    {
        $achievement->achievers()->sync(collect($this->idsBySlug($slugs, Achiever::class))
            ->mapWithKeys(fn (int $id, int $index) => [$id => ['is_primary' => $index === 0, 'sort_order' => $index]])
            ->all());
    }

    /**
     * Link achievements to an achiever. Existing links keep their order and primary flag; a new link is primary only
     * when the achievement had no achiever yet.
     *
     * @param  list<string>  $slugs
     */
    protected function syncAchievements(Achiever $achiever, array $slugs): void
    {
        $ids = $this->idsBySlug($slugs, Achievement::class);
        $current = $achiever->achievements()->pluck('achievements.id')->all();

        $achiever->achievements()->detach(array_diff($current, $ids));

        foreach (array_diff($ids, $current) as $id) {
            $others = Achievement::query()->find($id)?->achievers()->count() ?? 0;
            $achiever->achievements()->attach($id, ['is_primary' => $others === 0, 'sort_order' => $others]);
        }
    }

    /** @param  list<string>  $slugs */
    protected function syncOrganizations(Achievement $achievement, array $slugs): void
    {
        $achievement->organizations()->sync($this->idsBySlug($slugs, Organization::class));
    }

    /**
     * The old social / contact fields of an achiever are its SocialLink rows (null or '' removes one).
     *
     * @param  array<string, mixed>  $validated
     */
    protected function syncSocialLinks(Achiever $achiever, array $validated): void
    {
        foreach (self::SOCIAL_FIELDS as $field => $platform) {
            if (! array_key_exists($field, $validated)) {
                continue;
            }

            $value = trim((string) $validated[$field]);

            if ($value === '') {
                $achiever->socialLinks()->where('platform', $platform)->delete();

                continue;
            }

            $attributes = $platform === 'whatsapp'
                ? ['url' => 'https://wa.me/'.preg_replace('/\D+/', '', $value), 'handle' => $value]
                : ['url' => $value];

            $achiever->socialLinks()->updateOrCreate(['platform' => $platform], $attributes + ['is_active' => true]);
        }
    }

    /**
     * Arguments of the old dashboard that have no place in this app: accepted, not stored, reported in `notes`.
     *
     * @param  array<string, mixed>  $validated
     * @param  array<string, string>  $ignored  argument => reason
     * @return list<string>
     */
    protected function ignoredNotes(array $validated, array $ignored): array
    {
        return collect($ignored)
            ->filter(fn (string $reason, string $argument) => ($validated[$argument] ?? null) !== null)
            ->map(fn (string $reason, string $argument) => "`{$argument}` was not stored: {$reason}")
            ->values()->all();
    }

    /** @return array<string, mixed> */
    protected function achievementPayload(Achievement $achievement): array
    {
        $achievement->loadMissing(['scope', 'field', 'achievers', 'organizations', 'media']);

        return [
            'id' => $achievement->id,
            'slug' => $achievement->slug,
            'title_ar' => $this->translation($achievement, 'title', 'ar'),
            'title_en' => $this->translation($achievement, 'title', 'en'),
            'excerpt_ar' => $this->translation($achievement, 'excerpt', 'ar'),
            'excerpt_en' => $this->translation($achievement, 'excerpt', 'en'),
            'description_ar' => $this->translation($achievement, 'body', 'ar'),
            'description_en' => $this->translation($achievement, 'body', 'en'),
            'scope' => $this->taxonomy($achievement->scope),
            'main_type' => $this->taxonomy($achievement->field),
            'sub_type' => null,
            'discipline_ar' => $this->translation($achievement, 'discipline', 'ar'),
            'discipline_en' => $this->translation($achievement, 'discipline', 'en'),
            'country_ar' => $this->translation($achievement, 'country', 'ar'),
            'country_en' => $this->translation($achievement, 'country', 'en'),
            'video_url' => $achievement->video_url,
            'achieved_at' => $achievement->achieved_at?->toDateString(),
            'images' => $this->images($achievement, 'achievement'),
            'is_published' => $achievement->is_published,
            'sort_order' => $achievement->sort_order,
            'achievers' => $achievement->achievers->pluck('slug')->all(),
            'organizations' => $achievement->organizations->pluck('slug')->all(),
            'url' => $achievement->url(),
        ];
    }

    /** @return array<string, mixed> */
    protected function achieverPayload(Achiever $achiever): array
    {
        $achiever->loadMissing(['field', 'achievements', 'socialLinks', 'media']);
        $social = $achiever->socialLinks->keyBy('platform');

        return [
            'id' => $achiever->id,
            'slug' => $achiever->slug,
            'name_ar' => $this->translation($achiever, 'name', 'ar'),
            'name_en' => $this->translation($achiever, 'name', 'en'),
            'headline_ar' => $this->translation($achiever, 'headline', 'ar'),
            'headline_en' => $this->translation($achiever, 'headline', 'en'),
            'description_ar' => $this->translation($achiever, 'short_bio', 'ar'),
            'description_en' => $this->translation($achiever, 'short_bio', 'en'),
            'story_ar' => $this->translation($achiever, 'story', 'ar'),
            'story_en' => $this->translation($achiever, 'story', 'en'),
            'main_type' => $this->taxonomy($achiever->field),
            'gender' => $achiever->gender,
            'is_featured' => $achiever->is_featured,
            'in_home_carousel' => $achiever->show_in_home_strip,
            'is_published' => $achiever->is_published,
            'sort_order' => $achiever->sort_order,
            'linkedin_url' => $social->get('linkedin')?->url,
            'twitter_url' => $social->get('x')?->url,
            'instagram_url' => $social->get('instagram')?->url,
            'facebook_url' => $social->get('facebook')?->url,
            'whatsapp_number' => $social->get('whatsapp')?->handle ?: $social->get('whatsapp')?->url,
            'email' => null,
            'phone' => null,
            'images' => $this->images($achiever, 'achiever'),
            'achievements' => $achiever->achievements->pluck('slug')->all(),
            'url' => $achiever->url(),
        ];
    }

    /** @return array<string, array<string, mixed>|null> */
    private function images(Model&HasMedia $record, string $type): array
    {
        return collect(ImageUploader::slots($type))
            ->mapWithKeys(fn (string $slot) => [$slot => ImageUploader::describe($record, $type, $slot)])
            ->all();
    }

    /** @return array{id: int, slug: string, title_ar: ?string, title_en: ?string}|null */
    protected function taxonomy(Scope|Field|null $model): ?array
    {
        if ($model === null) {
            return null;
        }

        return [
            'id' => $model->id,
            'slug' => $model->slug,
            'title_ar' => $this->translation($model, 'name', 'ar'),
            'title_en' => $this->translation($model, 'name', 'en'),
        ];
    }

    /**
     * Validation of the achievement fields shared by create-achievement and update-achievement (old argument names).
     *
     * @return array<string, mixed>
     */
    protected function achievementRules(): array
    {
        return [
            ...$this->pair('title'),
            ...$this->pair('excerpt', false),
            ...$this->pair('description', false),
            ...$this->pair('country'),
            ...$this->pair('discipline'),
            'scope' => ['nullable', 'string', 'exists:scopes,slug'],
            'main_type' => ['nullable', 'string', 'exists:fields,slug'],
            'sub_type' => ['nullable', 'string', 'max:255'],
            'achieved_at' => ['nullable', 'date'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'achievers' => ['nullable', 'array'],
            'achievers.*' => ['string', $this->liveSlug('achievers')],
            'organizations' => ['nullable', 'array'],
            'organizations.*' => ['string', $this->liveSlug('organizations')],
        ];
    }

    /**
     * Validation of the achiever fields shared by create-achiever and update-achiever (old argument names).
     *
     * @return array<string, mixed>
     */
    protected function achieverRules(): array
    {
        return [
            ...$this->pair('name'),
            ...$this->pair('headline'),
            ...$this->pair('description', false),
            ...$this->pair('story', false),
            'main_type' => ['nullable', 'string', 'exists:fields,slug'],
            'gender' => ['nullable', 'string', Rule::in(array_keys(Achiever::GENDERS))],
            'is_featured' => ['nullable', 'boolean'],
            'in_home_carousel' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'whatsapp_number' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'achievements' => ['nullable', 'array'],
            'achievements.*' => ['string', $this->liveSlug('achievements')],
        ];
    }

    /** Old achievement arguments without a place in this app. */
    protected const IGNORED_ACHIEVEMENT = [
        'sub_type' => 'this site has no sub types (list-taxonomies returns none); use discipline_ar / discipline_en for the specialty.',
    ];

    /** Old achiever arguments without a place in this app. */
    protected const IGNORED_ACHIEVER = [
        'email' => 'achievers have no contact email on this site.',
        'phone' => 'achievers have no contact phone on this site.',
    ];

    /** Validation rule of an optional bilingual text pair. */
    protected function pair(string $prefix, int|bool $max = 255): array
    {
        $rule = $max === false ? ['nullable', 'string'] : ['nullable', 'string', 'max:'.$max];

        return ["{$prefix}_ar" => $rule, "{$prefix}_en" => $rule];
    }
}
