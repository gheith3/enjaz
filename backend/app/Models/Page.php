<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use App\Models\Concerns\LogsContentActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

/**
 * One of the site's fixed pages (home, about, achievers ...). Its structure is code; its texts, SEO and
 * the order/visibility of its sections are edited in the dashboard.
 */
class Page extends Model implements HasMedia
{
    use ClearsResponseCache, HasFactory, HasTranslations, InteractsWithMedia, LogsContentActivity;

    protected $fillable = [
        'key', 'title', 'meta_description', 'hero_eyebrow', 'hero_title', 'hero_lead',
        'hero_cta_label', 'hero_cta_url', 'noindex',
    ];

    /** @var list<string> */
    public array $translatable = ['title', 'meta_description', 'hero_eyebrow', 'hero_title', 'hero_lead', 'hero_cta_label'];

    protected function casts(): array
    {
        return ['noindex' => 'boolean'];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('og_image')->singleFile();
        $this->addMediaCollection('hero');
    }

    /** A letter shown on the page (e.g. His Majesty's handwritten letter on the organizations page). */
    public function letter(): MorphOne
    {
        return $this->morphOne(Letter::class, 'letterable');
    }

    /** Hero slider images (relation manager in the dashboard). */
    public function heroMedia(): MorphMany
    {
        return $this->media()->where('collection_name', 'hero');
    }

    /** Optional parts of this page's design (config/site.php "features"). */
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, config("site.pages.{$this->key}.features", []), true);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('sort_order');
    }

    /**
     * Enabled sections that have a component, in the editor's order.
     *
     * @return Collection<int, PageSection>
     */
    public function visibleSections(): Collection
    {
        return $this->sections->filter(fn (PageSection $section) => $section->is_enabled && $section->isRenderable())->values();
    }

    public function scopeKey(Builder $query, string $key): void
    {
        $query->where('key', $key);
    }

    public static function forKey(string $key): self
    {
        return static::query()->with(['sections', 'media'])->where('key', $key)->firstOr(
            fn () => new static(['key' => $key, 'title' => config("site.pages.$key.label", $key)])
        );
    }

    public function publicUrl(): ?string
    {
        $route = config("site.pages.{$this->key}.route");

        return $route && Route::has($route) ? route($route) : null;
    }

    /**
     * Create the pages and sections listed in config/site.php that are missing. Existing rows (and the
     * editors' order, visibility and texts) are left alone; sections no longer in the config are removed.
     */
    public static function syncFromConfig(): void
    {
        foreach (config('site.pages') as $key => $definition) {
            $page = static::firstOrCreate(['key' => $key], ['title' => ['ar' => $definition['label']]]);

            $keys = array_keys($definition['sections'] ?? []);
            $page->sections()->whereNotIn('key', $keys)->delete();
            $existing = $page->sections()->pluck('key')->all();
            $next = (int) $page->sections()->max('sort_order');

            foreach ($keys as $sectionKey) {
                if (! in_array($sectionKey, $existing, true)) {
                    $page->sections()->create(['key' => $sectionKey, 'sort_order' => ++$next]);
                }
            }
        }
    }

    /**
     * Section keys this page may contain (config/site.php).
     *
     * @return array<string, string>
     */
    public function availableSections(): array
    {
        return config("site.pages.{$this->key}.sections", []);
    }
}
