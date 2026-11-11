<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use App\Models\Concerns\LogsContentActivity;
use App\Support\Localized;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\View;

/**
 * A section of a fixed page (e.g. home.partners). The key picks the Blade component
 * resources/views/site/sections/<page>/<name>.blade.php; settings hold its small options (limit, title...).
 */
class PageSection extends Model
{
    use ClearsResponseCache, LogsContentActivity;

    protected $fillable = ['page_id', 'key', 'is_enabled', 'sort_order', 'settings'];

    protected function casts(): array
    {
        return ['is_enabled' => 'boolean', 'settings' => 'array', 'sort_order' => 'integer'];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PageSectionItem::class)->orderBy('sort_order');
    }

    /** A translatable setting ({ar, en}) in the current language, or the default. */
    public function text(string $key, ?string $default = null): ?string
    {
        return Localized::get($this->setting($key)) ?: $default;
    }

    public function view(): string
    {
        return 'site.sections.'.$this->key;
    }

    public function isRenderable(): bool
    {
        return View::exists($this->view());
    }

    public function setting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }

    public function label(): string
    {
        [$page] = explode('.', $this->key, 2);

        // Section keys contain a dot, so read the page's list and index it (config('a.b.home.x') would split the key).
        return config("site.pages.$page.sections", [])[$this->key] ?? $this->key;
    }
}
