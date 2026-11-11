<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use App\Models\Concerns\HasSiteMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

/**
 * A record picked by hand for a page section (e.g. the people of the home "الرياضة" row), with the section's own
 * image and texts. Empty texts fall back to the linked record.
 */
class PageSectionItem extends Model implements HasMedia
{
    use ClearsResponseCache, HasSiteMedia, HasTranslations;

    protected $fillable = ['page_section_id', 'itemable_type', 'itemable_id', 'title', 'label', 'text', 'url', 'link_label', 'settings', 'sort_order'];

    /** @var list<string> */
    public array $translatable = ['title', 'label', 'text', 'link_label'];

    protected function casts(): array
    {
        return ['settings' => 'array', 'sort_order' => 'integer'];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
        $this->addMediaCollection('image2')->singleFile();   // a second photo (e.g. the founder's card)
        $this->addMediaCollection('icon')->singleFile();
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(PageSection::class, 'page_section_id');
    }

    public function itemable(): MorphTo
    {
        return $this->morphTo();
    }

    /** The item's title, or the linked record's name / title. */
    public function displayTitle(): string
    {
        return (string) ($this->title ?: ($this->itemable?->name ?? $this->itemable?->title ?? ''));
    }

    /** The item's link, or the linked record's page when it is published. */
    public function href(): ?string
    {
        if ($this->url) {
            return $this->url;
        }
        $record = $this->itemable;

        return $record && method_exists($record, 'url') && (! method_exists($record, 'isLive') || $record->isLive()) ? $record->url() : null;
    }

    /** A flag set in the item's settings (e.g. «مميّز»: official testimonial). */
    public function flag(string $key): bool
    {
        return (bool) ($this->settings[$key] ?? false);
    }

    public function isExternal(): bool
    {
        return (bool) $this->url && ! str_starts_with($this->url, url('/')) && preg_match('#^https?://#', $this->url);
    }
}
