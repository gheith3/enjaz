<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use App\Models\Concerns\LogsContentActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Spatie\Translatable\HasTranslations;

/**
 * Links of the full-screen menu (location "header"), the desktop bar (header items flagged
 * show_in_desktop_nav, with their short label) and the two footer columns.
 */
class MenuItem extends Model
{
    use ClearsResponseCache, HasFactory, HasTranslations, LogsContentActivity;

    public const LOCATIONS = ['header', 'footer_1', 'footer_2'];

    private const CACHE_KEY = 'site.menu-rows';

    protected $fillable = ['location', 'label', 'label_short', 'url', 'show_in_desktop_nav', 'opens_new_tab', 'is_active', 'sort_order'];

    /** @var list<string> */
    public array $translatable = ['label', 'label_short'];

    protected function casts(): array
    {
        return [
            'show_in_desktop_nav' => 'boolean',
            'opens_new_tab' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        $forget = fn () => Cache::forget(self::CACHE_KEY);
        static::saved($forget);
        static::deleted($forget);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * All active items grouped by location, cached until an item changes.
     *
     * @return Collection<string, Collection<int, MenuItem>>
     */
    public static function grouped(): Collection
    {
        // Raw rows are cached (Laravel does not unserialize models from the cache) and turned back into models.
        $rows = Cache::rememberForever(self::CACHE_KEY, fn () => static::active()->orderBy('sort_order')->toBase()->get()
            ->map(fn ($row) => (array) $row)->all());

        return static::hydrate($rows)->groupBy('location');
    }

    public function href(): string
    {
        return str_starts_with($this->url, 'http') || str_starts_with($this->url, 'mailto:') || str_starts_with($this->url, 'tel:')
            ? $this->url
            : url($this->url);
    }

    public function isCurrent(): bool
    {
        if (str_starts_with($this->url, 'http')) {
            return false;
        }

        $path = trim($this->url, '/');

        return $path === '' ? request()->is('/') : request()->is($path, $path.'/*');
    }

    public function shortLabel(): string
    {
        return $this->label_short ?: $this->label;
    }
}
