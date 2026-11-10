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

/**
 * A winner of an edition: a person, an organization or a team (team names), with its category, rank and the
 * reason. Images: photo (focal point, credit), logo, designs (winning design images with captions), gallery.
 */
class AwardWinner extends Model implements HasMedia
{
    use ClearsResponseCache, HasSiteMedia, HasTranslations;

    protected $fillable = [
        'award_edition_id', 'award_category_id', 'achiever_id', 'rank', 'award_label', 'name', 'team', 'work', 'field', 'why', 'place',
        'gender', 'is_group', 'is_withheld', 'source_title', 'source_url', 'sort_order',
    ];

    /** @var list<string> */
    public array $translatable = ['award_label', 'name', 'work', 'field', 'why', 'place'];

    protected function casts(): array
    {
        return ['team' => 'array', 'rank' => 'integer', 'is_group' => 'boolean', 'is_withheld' => 'boolean'];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')->singleFile();
        $this->addMediaCollection('logo')->singleFile();
        $this->addMediaCollection('designs');
        $this->addMediaCollection('gallery');
    }

    public function edition(): BelongsTo
    {
        return $this->belongsTo(AwardEdition::class, 'award_edition_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AwardCategory::class, 'award_category_id');
    }

    public function achiever(): BelongsTo
    {
        return $this->belongsTo(Achiever::class);
    }

    public function facts(): MorphMany
    {
        return $this->morphMany(Fact::class, 'factable')->orderBy('sort_order');
    }

    /** @return list<string> */
    public function teamNames(): array
    {
        return array_values(array_filter(array_map(fn ($n) => Localized::get($n), $this->team ?? [])));
    }

    public function displayName(): string
    {
        return (string) ($this->name ?: implode(' · ', $this->teamNames()));
    }

    public function galleryMedia(): MorphMany
    {
        return $this->media()->where('collection_name', 'gallery');
    }

    public function designMedia(): MorphMany
    {
        return $this->media()->where('collection_name', 'designs');
    }
}
