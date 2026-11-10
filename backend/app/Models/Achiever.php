<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use App\Models\Concerns\HasPublishing;
use App\Models\Concerns\HasSiteMedia;
use App\Models\Concerns\LogsContentActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

/**
 * المنجزون. Images: portrait (cards), cutout (profile hero, ID card), avatar (small round), journey (the "مسيرتي"
 * photo), week (the "منجز الأسبوع" card; falls back to the cutout), gallery (المكتبة المرئية).
 */
class Achiever extends Model implements HasMedia
{
    use ClearsResponseCache, HasFactory, HasPublishing, HasSiteMedia, HasTranslations, LogsContentActivity, SoftDeletes;

    public const GENDERS = ['m' => 'ذكر', 'f' => 'أنثى'];

    protected $fillable = [
        'slug', 'name', 'gender', 'field_id', 'headline', 'hero_lines', 'short_bio', 'story', 'journey_kicker', 'journey_title',
        'personal_quote', 'personal_quote_context', 'personal_quote_source', 'quotes_note', 'portrait_trace', 'is_featured', 'is_inspiring',
        'inspiring_tagline', 'show_in_home_strip', 'week_from', 'week_teaser', 'is_published', 'published_at', 'sort_order',
    ];

    /** @var list<string> */
    public array $translatable = [
        'name', 'headline', 'hero_lines', 'short_bio', 'story', 'journey_kicker', 'journey_title', 'personal_quote',
        'personal_quote_context', 'personal_quote_source', 'quotes_note', 'inspiring_tagline', 'week_teaser',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean', 'is_inspiring' => 'boolean', 'show_in_home_strip' => 'boolean',
            'week_from' => 'date', 'sort_order' => 'integer',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('portrait')->singleFile();
        $this->addMediaCollection('cutout')->singleFile();
        $this->addMediaCollection('avatar')->singleFile();
        $this->addMediaCollection('journey')->singleFile();
        $this->addMediaCollection('week')->singleFile();
        $this->addMediaCollection('gallery');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class)->withPivot(['is_primary', 'sort_order']);
    }

    public function journeyItems(): HasMany
    {
        return $this->hasMany(AchieverJourneyItem::class)->orderBy('sort_order');
    }

    public function quotes(): MorphMany
    {
        return $this->morphMany(Quote::class, 'quotable')->orderBy('sort_order');
    }

    public function socialLinks(): MorphMany
    {
        return $this->morphMany(SocialLink::class, 'linkable')->orderBy('sort_order');
    }

    /** Gallery images (dashboard relation manager); the site uses getMedia('gallery'). */
    public function galleryMedia(): MorphMany
    {
        return $this->media()->where('collection_name', 'gallery');
    }

    public function publishedAchievements(): BelongsToMany
    {
        return $this->achievements()->published()->orderByDesc('achieved_at');
    }

    public function scopeListed(Builder $query): void
    {
        $query->published()->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('id');
    }

    /**
     * "منجز الأسبوع" and "منجزة الأسبوع": for each gender, the published achiever whose week started most recently.
     *
     * @return array<string, ?Achiever>
     */
    public static function ofTheWeek(): array
    {
        return collect(self::GENDERS)->map(fn ($label, $gender) => static::query()->published()->with(['field', 'media'])
            ->where('gender', $gender)->whereNotNull('week_from')->where('week_from', '<=', today())
            ->orderByDesc('week_from')->first())->all();
    }

    /**
     * Total / international / local counts of published achievements (profile medal and ID card).
     *
     * @return array{total: int, international: int, local: int}
     */
    public function medalStats(): array
    {
        $achievements = $this->relationLoaded('publishedAchievements')
            ? $this->publishedAchievements
            : $this->publishedAchievements()->with('scope')->get();
        $international = $achievements->filter(fn (Achievement $a) => $a->scope?->is_international)->count();

        return ['total' => $achievements->count(), 'international' => $international, 'local' => $achievements->count() - $international];
    }

    public function isFemale(): bool
    {
        return $this->gender === 'f';
    }

    /** Hero lines as a list (one per line). */
    public function heroLines(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/u', (string) $this->hero_lines))));
    }

    public function url(): string
    {
        return route('achievers.show', $this);
    }

    /**
     * Achievers of the same field (profile "منجزون في نفس المجال").
     *
     * @return Collection<int, Achiever>
     */
    public function related(int $limit = 12): Collection
    {
        if (! $this->field_id) {
            return collect();
        }

        return static::query()->listed()->with('media')->where('field_id', $this->field_id)->whereKeyNot($this->getKey())->limit($limit)->get();
    }
}
