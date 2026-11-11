<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use App\Models\Concerns\HasPublishing;
use App\Models\Concerns\HasSiteMedia;
use App\Models\Concerns\LogsContentActivity;
use App\Support\ArabicCount;
use App\Support\ArabicDate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

/**
 * مقال في مجلة إنجازات. published_at is the article's date. read_minutes is computed on save from the text (180 words a minute);
 * views count real reads (most read). Images: cover (with cover_caption).
 */
class Article extends Model implements HasMedia
{
    use ClearsResponseCache, HasFactory, HasPublishing, HasSiteMedia, HasTranslations, LogsContentActivity, SoftDeletes;

    protected $fillable = [
        'slug', 'title', 'dek', 'lead', 'body', 'pull_quote', 'box_title', 'box_items', 'cover_caption', 'tags', 'article_category_id',
        'writer_id', 'issue_id', 'read_minutes', 'show_on_home', 'is_published', 'published_at', 'sort_order',
    ];

    /** @var list<string> */
    public array $translatable = ['title', 'dek', 'lead', 'body', 'pull_quote', 'box_title', 'box_items', 'cover_caption', 'tags'];

    protected function casts(): array
    {
        return ['show_on_home' => 'boolean', 'read_minutes' => 'integer', 'views' => 'integer', 'sort_order' => 'integer'];
    }

    protected static function booted(): void
    {
        static::saving(function (Article $article) {
            $text = $article->lead.' '.strip_tags((string) $article->body).' '.$article->pull_quote.' '.implode(' ', (array) $article->box_items);
            $words = count(preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY));
            if ($words > 0 || ! $article->read_minutes) {
                $article->read_minutes = max(1, (int) round($words / 180));
            }
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id');
    }

    public function writer(): BelongsTo
    {
        return $this->belongsTo(Writer::class);
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }

    /** Published, newest first. */
    public function scopeListed(Builder $query): void
    {
        $query->published()->orderByDesc('published_at')->orderBy('sort_order')->orderByDesc('id');
    }

    public function dateLabel(): string
    {
        return ArabicDate::format($this->published_at, 'day');
    }

    /** "٦ دقائق" */
    public function minutesLabel(): string
    {
        return app()->getLocale() === 'ar'
            ? ArabicCount::of($this->read_minutes, 'minutes')
            : __('site.magazine.minutes', ['n' => $this->read_minutes]);
    }

    /** @return list<string> */
    public function tagList(): array
    {
        return array_values(array_filter((array) $this->tags));
    }

    /** @return list<string> */
    public function boxItems(): array
    {
        return array_values(array_filter((array) $this->box_items));
    }

    public function url(): string
    {
        return route('magazine.show', $this);
    }
}
