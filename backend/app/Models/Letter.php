<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use App\Models\Concerns\HasSiteMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

/** The leader's word (كلمة الرئيس / الوزير) of an organization or award. Image: photo (cut-out). */
class Letter extends Model implements HasMedia
{
    use ClearsResponseCache, HasSiteMedia, HasTranslations;

    protected $fillable = ['title', 'name', 'role', 'body', 'quote', 'meta', 'source_text', 'source_url', 'is_published'];

    /** @var list<string> */
    public array $translatable = ['title', 'name', 'role', 'body', 'quote', 'meta'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')->singleFile();
    }

    public function letterable(): MorphTo
    {
        return $this->morphTo();
    }

    /** @return list<string> */
    public function paragraphs(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R\s*\R/u', (string) $this->body))));
    }
}
