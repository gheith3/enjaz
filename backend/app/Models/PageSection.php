<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use App\Models\Concerns\LogsContentActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

        return config("site.pages.$page.sections.{$this->key}", $this->key);
    }
}
