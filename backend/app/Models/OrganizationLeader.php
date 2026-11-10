<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use App\Models\Concerns\HasSiteMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

/** Presidents (timeline), current leadership and board members. Image: photo (cut-out PNG). */
class OrganizationLeader extends Model implements HasMedia
{
    use ClearsResponseCache, HasSiteMedia, HasTranslations;

    public const KINDS = ['president' => 'الرؤساء المتعاقبون', 'leader' => 'القيادة الحالية', 'board' => 'مجلس الأمناء / الإدارة'];

    protected $fillable = ['organization_id', 'kind', 'name', 'title', 'role', 'from_year', 'to_year', 'is_current', 'note', 'sort_order'];

    /** @var list<string> */
    public array $translatable = ['name', 'title', 'role', 'note'];

    protected function casts(): array
    {
        return ['from_year' => 'integer', 'to_year' => 'integer', 'is_current' => 'boolean'];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')->singleFile();
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
