<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use App\Models\Concerns\HasSiteMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

/** "قالوا عن ..." on a profile, testimonials on the about page. Image: avatar. */
class Quote extends Model implements HasMedia
{
    use ClearsResponseCache, HasSiteMedia, HasTranslations;

    protected $fillable = ['type', 'text', 'name', 'role', 'sort_order'];

    /** @var list<string> */
    public array $translatable = ['text', 'name', 'role'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();
    }

    public function quotable(): MorphTo
    {
        return $this->morphTo();
    }
}
