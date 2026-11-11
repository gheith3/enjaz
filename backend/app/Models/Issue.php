<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use App\Support\ArabicDate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

/** An issue (العدد) of the magazine: number, date, tagline, lead article and quote. The newest published one is current. */
class Issue extends Model
{
    use ClearsResponseCache, HasTranslations;

    protected $fillable = ['number', 'issued_at', 'tagline', 'quote', 'quote_cite', 'lead_article_id', 'is_published'];

    /** @var list<string> */
    public array $translatable = ['tagline', 'quote', 'quote_cite'];

    protected function casts(): array
    {
        return ['issued_at' => 'date', 'number' => 'integer', 'is_published' => 'boolean'];
    }

    public function leadArticle(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'lead_article_id');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public static function current(): ?self
    {
        return static::query()->where('is_published', true)->orderByDesc('number')->first();
    }

    public function label(): string
    {
        return __('site.magazine.issue_n', ['n' => ArabicDate::digits($this->number)]);
    }

    public function dateLabel(): string
    {
        return ArabicDate::format($this->issued_at, 'month');
    }
}
