<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use App\Models\Concerns\LogsContentActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

/**
 * حكومي، عسكري، خاص، أكاديمي، رياضي. `template` picks the organization page design:
 * institution (institution.html), university (university.html) or federation (federation.html).
 */
class OrganizationType extends Model
{
    use ClearsResponseCache, HasFactory, HasTranslations, LogsContentActivity;

    public const TEMPLATES = ['institution' => 'مؤسسة', 'university' => 'جامعة / كلية', 'federation' => 'اتحاد رياضي'];

    protected $fillable = ['slug', 'name', 'label', 'group_label', 'template', 'css_class', 'sort_order'];

    /** @var list<string> */
    public array $translatable = ['name', 'label', 'group_label'];

    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }
}
