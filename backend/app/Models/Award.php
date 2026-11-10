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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

/**
 * الجوائز. `kind` picks the design: culture (award.html), volunteer (award-volunteer.html),
 * architecture (bilarab-award.html). Images: logo, mark (emblem), hero (background photo),
 * card_art (awards list trophy), vision_logo, emblem (blessing emblem).
 */
class Award extends Model implements HasMedia
{
    use ClearsResponseCache, HasFactory, HasPublishing, HasSiteMedia, HasTranslations, LogsContentActivity, SoftDeletes;

    public const KINDS = ['culture' => 'ثقافية (أوسمة ودورات بالتناوب)', 'volunteer' => 'تطوعية (منصات الأفراد والجمعيات)', 'architecture' => 'معمارية (دورات ومشاريع)'];

    protected $fillable = [
        'slug', 'kind', 'name', 'title', 'subtitle', 'eyebrow', 'motto', 'date_line', 'decree', 'founded_year', 'launched',
        'website', 'about', 'lead', 'vision', 'honor', 'firsts', 'extra', 'card_meta', 'card_text', 'card_style',
        'is_published', 'published_at', 'sort_order',
    ];

    /** @var list<string> */
    public array $translatable = [
        'name', 'title', 'subtitle', 'eyebrow', 'motto', 'date_line', 'launched', 'about', 'lead', 'vision', 'honor', 'firsts',
        'card_meta', 'card_text',
    ];

    protected function casts(): array
    {
        return ['extra' => 'array', 'founded_year' => 'integer', 'is_published' => 'boolean', 'published_at' => 'datetime'];
    }

    public function registerMediaCollections(): void
    {
        foreach (['logo', 'mark', 'hero', 'card_art', 'vision_logo', 'emblem'] as $collection) {
            $this->addMediaCollection($collection)->singleFile();
        }
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function categories(): HasMany
    {
        return $this->hasMany(AwardCategory::class)->orderBy('sort_order');
    }

    public function editions(): HasMany
    {
        return $this->hasMany(AwardEdition::class)->orderBy('sort_order');
    }

    public function winners(): HasManyThrough
    {
        return $this->hasManyThrough(AwardWinner::class, AwardEdition::class);
    }

    public function features(): HasMany
    {
        return $this->hasMany(AwardFeature::class)->orderBy('sort_order');
    }

    public function facts(): MorphMany
    {
        return $this->morphMany(Fact::class, 'factable')->orderBy('sort_order');
    }

    public function sources(): MorphMany
    {
        return $this->morphMany(Source::class, 'sourceable')->orderBy('sort_order');
    }

    public function letter(): MorphOne
    {
        return $this->morphOne(Letter::class, 'letterable');
    }

    public function scopeListed(Builder $query): void
    {
        $query->published()->orderBy('sort_order')->orderBy('id');
    }

    /** @return Collection<int, Fact> */
    public function factsIn(string $group): Collection
    {
        return $this->facts->where('group', $group)->values();
    }

    /** @return Collection<string, AwardCategory> keyed by key */
    public function categoriesOf(string $kind): Collection
    {
        return $this->categories->where('kind', $kind)->keyBy('key');
    }

    /** A small design label stored in `extra` ({key: {ar, en}}), or the default. */
    public function label(string $key, ?string $default = null): ?string
    {
        return Localized::get($this->extra[$key] ?? null) ?: $default;
    }

    public function url(): string
    {
        return route('awards.show', $this);
    }
}
