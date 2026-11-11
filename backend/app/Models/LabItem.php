<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use App\Models\Concerns\HasPublishing;
use App\Models\Concerns\HasSiteMedia;
use App\Models\Concerns\LogsContentActivity;
use App\Support\ArabicDate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

/**
 * المختبر: an invention (medal at an exhibition, story) or a scientific publication (finding, summary, citation).
 * Images: cover (the invention photo, or the publication's illustration), images (inside the story text).
 */
class LabItem extends Model implements HasMedia
{
    use ClearsResponseCache, HasFactory, HasPublishing, HasSiteMedia, HasTranslations, LogsContentActivity, SoftDeletes;

    public const KINDS = ['invention' => 'اختراع / ابتكار', 'publication' => 'منشور علمي'];

    /** URL segment of each kind on /lab/{kind}. */
    public const SEGMENTS = ['invention' => 'inventions', 'publication' => 'publications'];

    public const MEDALS = ['gold' => 'ذهبية', 'silver' => 'فضية', 'bronze' => 'برونزية'];

    protected $fillable = [
        'kind', 'slug', 'title', 'short', 'lead', 'body', 'summary', 'who', 'authors', 'journal', 'pub_type', 'field',
        'finding_value', 'finding_text', 'organization_id', 'org_label', 'achiever_id', 'medal', 'event', 'event_label', 'place',
        'happened_at', 'date_precision', 'credit_text', 'credit_url', 'cover_is_illustrative', 'is_published', 'published_at', 'sort_order',
    ];

    /** @var list<string> */
    public array $translatable = [
        'title', 'short', 'lead', 'body', 'summary', 'who', 'authors', 'journal', 'pub_type', 'field', 'finding_value', 'finding_text',
        'org_label', 'event_label', 'place',
    ];

    protected function casts(): array
    {
        return ['happened_at' => 'date', 'cover_is_illustrative' => 'boolean', 'sort_order' => 'integer'];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
        $this->addMediaCollection('images');   // images inside the story text
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function achiever(): BelongsTo
    {
        return $this->belongsTo(Achiever::class);
    }

    public function facts(): MorphMany
    {
        return $this->morphMany(Fact::class, 'factable')->orderBy('sort_order');
    }

    public function sources(): MorphMany
    {
        return $this->morphMany(Source::class, 'sourceable')->orderBy('sort_order');
    }

    public function scopeOfKind(Builder $query, string $kind): void
    {
        $query->where('kind', $kind);
    }

    /** Published, newest first (as the design sorts them). */
    public function scopeListed(Builder $query): void
    {
        $query->published()->orderByDesc('happened_at')->orderBy('sort_order')->orderBy('id');
    }

    public function isInvention(): bool
    {
        return $this->kind === 'invention';
    }

    public function year(): ?int
    {
        return $this->happened_at?->year;
    }

    public function dateLabel(): string
    {
        return ArabicDate::format($this->happened_at, $this->date_precision ?: 'month');
    }

    public function medalLabel(): string
    {
        return $this->medal ? __('site.lab.medal_'.$this->medal) : '';
    }

    public function orgName(): string
    {
        return (string) ($this->organization?->name ?: $this->org_label);
    }

    public function cardTitle(): string
    {
        return (string) ($this->short ?: $this->title);
    }

    /** Minutes to read the story (design: words / 180, at least one). */
    public function readMinutes(): int
    {
        $words = count(preg_split('/\s+/u', trim(strip_tags($this->lead.' '.$this->body)), -1, PREG_SPLIT_NO_EMPTY));

        return max(1, (int) round($words / 180));
    }

    /** "Authors (year). Title. Journal." */
    public function citation(): string
    {
        return trim($this->authors.' ('.$this->year().'). '.$this->title.'. '.$this->journal.'.');
    }

    public function segment(): string
    {
        return self::SEGMENTS[$this->kind] ?? 'inventions';
    }

    public function url(): string
    {
        return route('lab.show', $this);
    }
}
