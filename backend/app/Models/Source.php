<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Translatable\HasTranslations;

/** A reference (المصادر) of an achievement, organization, award ... */
class Source extends Model
{
    use ClearsResponseCache, HasTranslations;

    protected $fillable = ['title', 'subtitle', 'url', 'sort_order'];

    /** @var list<string> */
    public array $translatable = ['title', 'subtitle'];

    public function sourceable(): MorphTo
    {
        return $this->morphTo();
    }
}
