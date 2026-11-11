<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use App\Models\Concerns\HasPublishing;
use App\Models\Concerns\HasSiteMedia;
use App\Models\Concerns\LogsContentActivity;
use App\Support\ArabicDate;
use App\Support\Localized;
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
 * الإنجازات. One or more achievers (one primary). Image: cover (with focal point).
 */
class Achievement extends Model implements HasMedia
{
    use ClearsResponseCache, HasFactory, HasPublishing, HasSiteMedia, HasTranslations, LogsContentActivity, SoftDeletes;

    public const PRECISIONS = ['day' => 'اليوم والشهر والسنة', 'month' => 'الشهر والسنة', 'year' => 'السنة فقط'];

    protected $fillable = [
        'slug', 'title', 'excerpt', 'body', 'scope_id', 'field_id', 'discipline', 'profile_tag', 'achieved_at', 'date_precision',
        'country', 'highlights', 'pull_quote', 'pull_quote_by', 'pull_quote_role', 'pull_quote_note', 'pull_quote_after',
        'video_url', 'is_featured', 'show_on_home', 'home_slot', 'home_title', 'is_published', 'published_at', 'sort_order',
    ];

    /** @var list<string> */
    public array $translatable = [
        'title', 'home_title', 'excerpt', 'body', 'discipline', 'profile_tag', 'country', 'pull_quote', 'pull_quote_by', 'pull_quote_role', 'pull_quote_note',
    ];

    protected function casts(): array
    {
        return [
            'achieved_at' => 'date', 'highlights' => 'array', 'is_featured' => 'boolean', 'show_on_home' => 'boolean',
            'pull_quote_after' => 'integer', 'sort_order' => 'integer',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scope(): BelongsTo
    {
        return $this->belongsTo(Scope::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function achievers(): BelongsToMany
    {
        return $this->belongsToMany(Achiever::class)->withPivot(['is_primary', 'sort_order'])
            ->orderByPivot('is_primary', 'desc')->orderByPivot('sort_order');
    }

    /** Organizations the achievement belongs to (a university's own achievement, a federation's ...). */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class);
    }

    /** Pivot rows, edited as a sortable list in the dashboard. */
    public function achieverLinks(): HasMany
    {
        return $this->hasMany(AchievementAchiever::class)->orderBy('sort_order');
    }

    public function sources(): MorphMany
    {
        return $this->morphMany(Source::class, 'sourceable')->orderBy('sort_order');
    }

    public function scopeListed(Builder $query): void
    {
        $query->published()->orderByDesc('achieved_at')->orderBy('id');
    }

    /** The primary achiever (first of the loaded relation). */
    public function primaryAchiever(): ?Achiever
    {
        return $this->achievers->first();
    }

    public function dateLabel(): string
    {
        return ArabicDate::format($this->achieved_at, $this->date_precision);
    }

    public function disciplineLabel(): string
    {
        return $this->discipline ?: (string) $this->field?->name;
    }

    /**
     * @return list<array{lead: string, text: string}>
     */
    public function highlightItems(): array
    {
        return collect($this->highlights ?? [])
            ->map(fn ($item) => ['lead' => Localized::get($item['lead'] ?? null), 'text' => Localized::get($item['text'] ?? null)])
            ->filter(fn ($item) => $item['lead'] !== '' || $item['text'] !== '')->values()->all();
    }

    /**
     * Body split into paragraphs (<p>...</p>) so the pull quote can sit between them.
     *
     * @return list<string>
     */
    public function bodyParagraphs(): array
    {
        preg_match_all('#<(p|h[2-4]|ul|ol|blockquote)\b.*?</\1>#su', (string) $this->body, $blocks);

        return $blocks[0] ?: array_filter([(string) $this->body]);
    }

    /** Words per minute of the design (js/achievement-enhance.js): 180. */
    public function readMinutes(): int
    {
        $words = count(preg_split('/\s+/u', trim(strip_tags($this->body.' '.$this->excerpt)), -1, PREG_SPLIT_NO_EMPTY));

        return max(1, (int) ceil($words / 180));
    }

    /**
     * Other published achievements of the primary achiever, newest first.
     *
     * @return Collection<int, Achievement>
     */
    public function siblings(): Collection
    {
        $achiever = $this->primaryAchiever();

        return $achiever
            ? $achiever->publishedAchievements()->with(['field', 'scope', 'media'])->get()
            : collect();
    }

    public function url(): string
    {
        return route('achievements.show', $this);
    }
}
