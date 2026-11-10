<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Translatable\HasTranslations;

/** A dated milestone of an organization (later: the about-page roadmap). */
class Milestone extends Model
{
    use ClearsResponseCache, HasTranslations;

    protected $fillable = ['year', 'title', 'text', 'place', 'sort_order'];

    /** @var list<string> */
    public array $translatable = ['title', 'text', 'place'];

    public function milestoneable(): MorphTo
    {
        return $this->morphTo();
    }
}
