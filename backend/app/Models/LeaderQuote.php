<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use App\Models\Concerns\HasSiteMedia;
use App\Models\Concerns\LogsContentActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

/** A slide of the home hero (كلمات القيادة): the leader's words and transparent portrait. */
class LeaderQuote extends Model implements HasMedia
{
    use ClearsResponseCache, HasSiteMedia, HasTranslations, LogsContentActivity;

    /** Slide layouts of the design (portrait size and position). */
    public const VARIANTS = ['sultan' => 'صورة كبيرة مع الشعار (جلالة السلطان)', 'ahd' => 'صورة وسطى (السيدة الجليلة)', 'dy' => 'صورة وسطى (صاحب السمو)'];

    protected $fillable = ['variant', 'honorific', 'name', 'postnominal', 'lead', 'body', 'is_active', 'sort_order'];

    /** @var list<string> */
    public array $translatable = ['honorific', 'name', 'postnominal', 'lead', 'body'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('portrait')->singleFile();
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    public function fullName(): string
    {
        return trim($this->honorific.' '.$this->name);
    }
}
