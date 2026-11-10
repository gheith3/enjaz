<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use App\Models\Concerns\HasPublishing;
use App\Models\Concerns\HasSiteMedia;
use App\Models\Concerns\LogsContentActivity;
use App\Support\Localized;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Vite;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

/**
 * المؤسسات: ministries, military, companies, universities/colleges and sports federations.
 * Images: logo, logo_wide (horizontal plate), logo_white (on dark backgrounds), cover, gallery (photos and documents: custom properties
 * title, year, note, era, chapter, kind photo|doc, credit).
 */
class Organization extends Model implements HasMedia
{
    use ClearsResponseCache, HasFactory, HasPublishing, HasSiteMedia, HasTranslations, LogsContentActivity, SoftDeletes;

    public const ACADEMIC_GROUPS = ['uni' => 'جامعة', 'college' => 'كلية', 'tech' => 'جامعة التقنية والعلوم التطبيقية'];

    public const SECTORS = ['gov' => 'حكومية', 'private' => 'خاصة', 'ahli' => 'أهلية'];

    protected $fillable = [
        'slug', 'organization_type_id', 'name', 'short_name', 'abbr', 'academic_group', 'sector', 'city', 'hq', 'sport',
        'founded', 'declared', 'formerly', 'tagline', 'about', 'website', 'color_primary', 'color_secondary', 'logo_ratio',
        'focus_areas', 'memberships', 'rankings', 'ranking_label', 'ranking_highlights', 'ranking_note', 'gallery_intro',
        'gallery_chapters', 'gallery_credit', 'is_featured', 'is_partner', 'is_published', 'published_at', 'sort_order',
    ];

    /** @var list<string> */
    public array $translatable = ['name', 'short_name', 'city', 'hq', 'sport', 'formerly', 'tagline', 'about', 'ranking_label', 'ranking_note', 'gallery_intro'];

    protected function casts(): array
    {
        return [
            'founded' => 'integer', 'declared' => 'integer', 'logo_ratio' => 'float', 'is_featured' => 'boolean', 'is_partner' => 'boolean',
            'focus_areas' => 'array', 'memberships' => 'array', 'rankings' => 'array', 'ranking_highlights' => 'array',
            'gallery_chapters' => 'array', 'gallery_credit' => 'array', 'sort_order' => 'integer',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
        $this->addMediaCollection('logo_wide')->singleFile();
        $this->addMediaCollection('logo_white')->singleFile();
        $this->addMediaCollection('cover')->singleFile();
        $this->addMediaCollection('gallery');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(OrganizationType::class, 'organization_type_id');
    }

    public function stats(): HasMany
    {
        return $this->hasMany(OrganizationStat::class)->orderBy('sort_order');
    }

    public function units(): HasMany
    {
        return $this->hasMany(OrganizationUnit::class)->orderBy('sort_order');
    }

    public function leaders(): HasMany
    {
        return $this->hasMany(OrganizationLeader::class)->orderBy('sort_order');
    }

    public function videos(): HasMany
    {
        return $this->hasMany(OrganizationVideo::class)->orderBy('sort_order');
    }

    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }

    public function milestones(): MorphMany
    {
        return $this->morphMany(Milestone::class, 'milestoneable')->orderBy('sort_order');
    }

    public function sources(): MorphMany
    {
        return $this->morphMany(Source::class, 'sourceable')->orderBy('sort_order');
    }

    public function letter(): MorphOne
    {
        return $this->morphOne(Letter::class, 'letterable');
    }

    public function galleryMedia(): MorphMany
    {
        return $this->media()->where('collection_name', 'gallery');
    }

    /** Achievements of the organization itself (not of its people). */
    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class);
    }

    /** People linked to the organization: staff, alumni, students, members (pivot kind + role). */
    public function achievers(): BelongsToMany
    {
        return $this->belongsToMany(Achiever::class)->using(AchieverOrganization::class)->withPivot(['id', 'kind', 'role', 'sort_order'])->orderByPivot('sort_order');
    }

    public function scopeOfType(Builder $query, string $slug): void
    {
        $query->whereHas('type', fn ($q) => $q->where('slug', $slug));
    }

    public function scopeListed(Builder $query): void
    {
        $query->published()->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Published achievements of the organization and of its published people, newest first, each tagged
     * with its source: org | student | staff | alum | member (design js/org-links.js achievementsFor()).
     *
     * @return Collection<int, array{achievement: Achievement, type: string}>
     */
    public function allAchievements(): Collection
    {
        $own = $this->achievements()->published()->with(['field', 'scope', 'media', 'achievers.media'])->get()
            ->map(fn (Achievement $a) => ['achievement' => $a, 'type' => 'org']);

        $members = $this->achievers()->published()->get();
        $kinds = $members->mapWithKeys(fn (Achiever $a) => [$a->id => $a->pivot->kind]);
        $people = Achievement::query()->published()->with(['field', 'scope', 'media', 'achievers.media'])
            ->whereHas('achievers', fn ($q) => $q->whereIn('achievers.id', $kinds->keys()))->get()
            ->map(fn (Achievement $a) => ['achievement' => $a, 'type' => $kinds[$a->achievers->firstWhere(fn ($x) => $kinds->has($x->id))?->id] ?? 'member']);

        return $own->concat($people)->unique(fn ($row) => $row['achievement']->id)
            ->sortByDesc(fn ($row) => $row['achievement']->achieved_at)->values();
    }

    /** The list page this organization belongs to (crumb and back links). */
    public function listUrl(): string
    {
        return route($this->template() === 'university' ? 'universities.index' : 'organizations.index');
    }

    /** Logo URL (original file: logos are often SVG/transparent PNG); military without a logo get the design's icon. */
    public function logoUrl(): ?string
    {
        $logo = $this->image('logo');
        if ($logo) {
            return $logo->getUrl();
        }

        return $this->type?->slug === 'military' ? Vite::asset('resources/site/assets/svg/cat-military.svg') : null;
    }

    /** "الجامعة" or "الكلية" (design: the page's wording follows the institution kind). */
    public function kindLabel(): string
    {
        return __('site.university.kind_'.($this->academic_group === 'college' ? 'college' : 'uni'));
    }

    /** Achievement source shown on university pages: org | staff | student (alumni and members count as students). */
    public static function displayType(string $type): string
    {
        return match ($type) {
            'org' => 'org', 'staff' => 'staff', default => 'student',
        };
    }

    /** Label of an achiever's link with the organization (design js/org-links.js kindLabel()). */
    public static function affiliationLabel(?string $kind, ?string $gender): string
    {
        $suffix = $gender === 'f' ? '_f' : '_m';

        return match ($kind) {
            'staff' => __('site.university.kind_staff'),
            'alum' => __('site.university.kind_alum'.$suffix),
            'student' => __('site.university.kind_student'.$suffix),
            default => __('site.university.kind_member'),
        };
    }

    public function template(): string
    {
        return $this->type?->template ?? 'institution';
    }

    public function displayName(): string
    {
        return $this->short_name ?: $this->name;
    }

    /** Logo shown as a square emblem (ratio < 1.6) or a wide plate. */
    public function hasWideLogo(): bool
    {
        return ($this->logo_ratio ?? 1) >= 1.6;
    }

    public function url(): string
    {
        return route('organizations.show', $this);
    }

    /** @return list<string> */
    public function focusAreaLabels(): array
    {
        return array_values(array_filter(array_map(fn ($area) => Localized::get($area), $this->focus_areas ?? [])));
    }
}
