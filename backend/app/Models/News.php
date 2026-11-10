<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use App\Models\Concerns\HasPublishing;
use App\Models\Concerns\HasSiteMedia;
use App\Models\Concerns\LogsContentActivity;
use App\Support\ArabicDate;
use App\Support\Localized;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

/**
 * News and organization achievements (design org-news.html). Images: cover, gallery.
 * Phase 4 adds categories for the public news section.
 */
class News extends Model implements HasMedia
{
    use ClearsResponseCache, HasFactory, HasPublishing, HasSiteMedia, HasTranslations, LogsContentActivity, SoftDeletes;

    public const KINDS = ['news' => 'خبر', 'achievement' => 'إنجاز'];

    protected $fillable = [
        'slug', 'organization_id', 'kind', 'tag', 'title', 'dek', 'summary', 'body', 'facts', 'cover_caption', 'happened_at',
        'is_published', 'published_at', 'sort_order',
    ];

    /** @var list<string> */
    public array $translatable = ['tag', 'title', 'dek', 'summary', 'body', 'cover_caption'];

    protected function casts(): array
    {
        return ['facts' => 'array', 'happened_at' => 'date', 'sort_order' => 'integer'];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
        $this->addMediaCollection('gallery');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function galleryMedia(): MorphMany
    {
        return $this->media()->where('collection_name', 'gallery');
    }

    public function dateLabel(string $precision = 'day'): string
    {
        return ArabicDate::format($this->happened_at, $precision);
    }

    /** @return list<array{label: string, value: string}> */
    public function factItems(): array
    {
        return collect($this->facts ?? [])->map(fn ($f) => ['label' => Localized::get($f['label'] ?? null), 'value' => Localized::get($f['value'] ?? null)])
            ->filter(fn ($f) => $f['label'] !== '' || $f['value'] !== '')->values()->all();
    }

    public function url(): string
    {
        return route('news.show', $this);
    }
}
