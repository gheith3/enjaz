<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

/** A college, unit, research center or branch of a university. */
class OrganizationUnit extends Model
{
    use ClearsResponseCache, HasTranslations;

    public const KINDS = ['college' => 'كلية', 'unit' => 'وحدة / قسم', 'center' => 'مركز', 'branch' => 'فرع'];

    protected $fillable = ['organization_id', 'kind', 'group_label', 'year', 'name', 'note', 'sort_order'];

    /** @var list<string> */
    public array $translatable = ['group_label', 'name', 'note'];

    protected function casts(): array
    {
        return ['year' => 'integer'];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
