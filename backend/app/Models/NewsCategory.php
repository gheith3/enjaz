<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

/** A section of the news page (news.html?category=…), e.g. الابتكارات والاختراعات والمنشورات العلمية. */
class NewsCategory extends Model
{
    use ClearsResponseCache, HasTranslations;

    protected $fillable = ['slug', 'name', 'intro', 'sort_order'];

    /** @var list<string> */
    public array $translatable = ['name', 'intro'];

    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }

    public function url(): string
    {
        return route('news.index', ['category' => $this->slug]);
    }
}
