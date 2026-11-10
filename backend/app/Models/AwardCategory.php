<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

/** Branches (culture/arts/lit), prizes (oman/arab), categories (individuals/associations) and fields of an award. */
class AwardCategory extends Model
{
    use ClearsResponseCache, HasTranslations;

    public const KINDS = ['branch' => 'فرع', 'prize' => 'نوع الدورة / الوسام', 'category' => 'فئة تنافس', 'field' => 'مجال'];

    protected $fillable = ['award_id', 'kind', 'key', 'name', 'hint', 'medal', 'basis', 'sort_order'];

    /** @var list<string> */
    public array $translatable = ['name', 'hint', 'medal', 'basis'];

    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }
}
