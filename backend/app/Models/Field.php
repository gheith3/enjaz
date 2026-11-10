<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use App\Models\Concerns\LogsContentActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Vite;
use Spatie\Translatable\HasTranslations;

/**
 * المجالات: sports, culture, science ... Used by achievers, achievements and the category chips.
 */
class Field extends Model
{
    use ClearsResponseCache, HasFactory, HasTranslations, LogsContentActivity;

    /** Category icons bundled from the design (resources/site/assets/svg/<icon>.svg). */
    public const ICONS = ['cat-sports', 'cat-culture', 'cat-science', 'cat-military', 'cat-medical', 'cat-innovation', 'cat-literature', 'cat-students', 'cat-arts', 'cat-business'];

    protected $fillable = ['slug', 'name', 'description', 'icon', 'card_tone', 'is_active', 'sort_order'];

    /** @var list<string> */
    public array $translatable = ['name', 'description'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function achievers(): HasMany
    {
        return $this->hasMany(Achiever::class);
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order');
    }

    public function iconUrl(): ?string
    {
        return $this->icon ? Vite::asset("resources/site/assets/svg/{$this->icon}.svg") : null;
    }
}
