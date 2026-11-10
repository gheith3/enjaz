<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use App\Models\Concerns\HasSiteMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

/** A cycle (دورة) of an award. Images: cover (caption + credit), gallery (project and ceremony photos). */
class AwardEdition extends Model implements HasMedia
{
    use ClearsResponseCache, HasSiteMedia, HasTranslations;

    public const TYPES = ['oman' => 'دورة عُمانية', 'arab' => 'دورة عربية', 'pause' => 'توقّف'];

    protected $fillable = [
        'award_id', 'number', 'year', 'type', 'title', 'note', 'status', 'place', 'area', 'entries_count', 'finals_count', 'brief',
        'jury', 'jury_all', 'exec', 'ceremony', 'withheld', 'open_fields', 'map_label', 'map_x', 'map_y', 'is_current', 'is_partial', 'sort_order',
    ];

    /** @var list<string> */
    public array $translatable = ['title', 'note', 'status', 'place', 'brief', 'jury', 'jury_all', 'exec', 'ceremony', 'map_label'];

    protected function casts(): array
    {
        return [
            'number' => 'integer', 'entries_count' => 'integer', 'finals_count' => 'integer', 'withheld' => 'array', 'open_fields' => 'array',
            'map_x' => 'float', 'map_y' => 'float', 'is_current' => 'boolean', 'is_partial' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
        $this->addMediaCollection('gallery');
    }

    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }

    public function winners(): HasMany
    {
        return $this->hasMany(AwardWinner::class)->orderBy('sort_order');
    }

    public function label(): string
    {
        return __('site.awards.edition_n', ['ord' => __('site.awards.ordinals_f')[$this->number] ?? $this->number]);
    }

    public function galleryMedia(): MorphMany
    {
        return $this->media()->where('collection_name', 'gallery');
    }
}
