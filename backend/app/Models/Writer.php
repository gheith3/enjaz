<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use App\Models\Concerns\HasPublishing;
use App\Models\Concerns\HasSiteMedia;
use App\Models\Concerns\LogsContentActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

/** أقلام إنجازات: a writer of the magazine. Images: photo. */
class Writer extends Model implements HasMedia
{
    use ClearsResponseCache, HasFactory, HasPublishing, HasSiteMedia, HasTranslations, LogsContentActivity, SoftDeletes;

    protected $fillable = [
        'slug', 'name', 'role', 'bio', 'quote', 'article_category_id', 'achiever_id', 'is_featured', 'is_published', 'published_at', 'sort_order',
    ];

    /** @var list<string> */
    public array $translatable = ['name', 'role', 'bio', 'quote'];

    protected function casts(): array
    {
        return ['is_featured' => 'boolean', 'sort_order' => 'integer'];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')->singleFile();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id');
    }

    public function achiever(): BelongsTo
    {
        return $this->belongsTo(Achiever::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function photoUrl(): ?string
    {
        return $this->imageUrl('photo', 'card');
    }

    /** Anchor of the writer's card on the writers page. */
    public function url(): string
    {
        return route('magazine.writers').'#'.$this->slug;
    }
}
