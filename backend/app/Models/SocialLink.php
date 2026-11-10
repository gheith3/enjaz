<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use App\Models\Concerns\LogsContentActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * A social account. linkable = null means the site's own accounts (footer, contact page);
 * otherwise it belongs to an achiever, writer ... A link without url is shown as "not available".
 */
class SocialLink extends Model
{
    use ClearsResponseCache, HasFactory, LogsContentActivity;

    /** Platforms with an icon in resources/views/components/site/social-icon.blade.php. */
    public const PLATFORMS = ['instagram', 'whatsapp', 'x', 'facebook', 'linkedin', 'youtube', 'snapchat', 'tiktok', 'website'];

    protected $fillable = ['linkable_type', 'linkable_id', 'platform', 'url', 'handle', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForSite(Builder $query): void
    {
        $query->whereNull('linkable_type')->where('is_active', true)->orderBy('sort_order');
    }

    public function label(): string
    {
        return __('site.social.'.$this->platform);
    }
}
