<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

/** A YouTube video of the organization's library / gallery. */
class OrganizationVideo extends Model
{
    use ClearsResponseCache, HasTranslations;

    protected $fillable = ['organization_id', 'youtube_id', 'title', 'year', 'duration', 'era', 'note', 'is_official', 'sort_order'];

    /** @var list<string> */
    public array $translatable = ['title', 'note'];

    protected function casts(): array
    {
        return ['is_official' => 'boolean'];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function thumbnail(): string
    {
        return 'https://i.ytimg.com/vi/'.$this->youtube_id.'/hqdefault.jpg';
    }
}
