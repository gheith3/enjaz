<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

/** A number on the organization hero: value (e.g. "+٢٠") and label ("كلية"). */
class OrganizationStat extends Model
{
    use ClearsResponseCache, HasTranslations;

    protected $fillable = ['organization_id', 'value', 'label', 'sort_order'];

    /** @var list<string> */
    public array $translatable = ['value', 'label'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
