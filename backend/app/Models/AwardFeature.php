<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use App\Models\Concerns\HasSiteMedia;
use App\Support\Localized;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

/** An extra section of an award page (special competition, initiative). Images: images (caption, credit, credit_url). */
class AwardFeature extends Model implements HasMedia
{
    use ClearsResponseCache, HasSiteMedia, HasTranslations;

    protected $fillable = ['award_id', 'key', 'chip', 'title', 'subtitle', 'text', 'note', 'team', 'entries_count', 'sort_order'];

    /** @var list<string> */
    public array $translatable = ['chip', 'title', 'subtitle', 'text', 'note'];

    protected function casts(): array
    {
        return ['team' => 'array', 'entries_count' => 'integer'];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images');
    }

    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }

    /** @return list<string> */
    public function teamNames(): array
    {
        return array_values(array_filter(array_map(fn ($n) => Localized::get($n), $this->team ?? [])));
    }

    public function imageMedia(): MorphMany
    {
        return $this->media()->where('collection_name', 'images');
    }
}
