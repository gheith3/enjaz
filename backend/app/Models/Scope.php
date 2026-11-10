<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use App\Models\Concerns\LogsContentActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * المستوى: local, gulf, arab, regional, international. is_international decides the profile's
 * "دولي / محلي" counts.
 */
class Scope extends Model
{
    use ClearsResponseCache, HasFactory, HasTranslations, LogsContentActivity;

    protected $fillable = ['slug', 'name', 'achievement_label', 'is_international', 'sort_order'];

    /** @var list<string> */
    public array $translatable = ['name', 'achievement_label'];

    protected function casts(): array
    {
        return ['is_international' => 'boolean', 'sort_order' => 'integer'];
    }

    /** CSS modifier of the design's tags: scope-international / scope-local. */
    public function cssGroup(): string
    {
        return $this->is_international ? 'international' : 'local';
    }
}
