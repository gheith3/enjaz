<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

/** A tab of the magazine (رأي، قصص ملهمة، علوم ...), for articles and writers. */
class ArticleCategory extends Model
{
    use ClearsResponseCache, HasTranslations;

    protected $fillable = ['slug', 'name', 'sort_order'];

    /** @var list<string> */
    public array $translatable = ['name'];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function writers(): HasMany
    {
        return $this->hasMany(Writer::class);
    }
}
