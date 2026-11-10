<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Translatable\HasTranslations;

/** A label/value line of a record, in groups (award goals, who can enter, path, criteria; winner facts). */
class Fact extends Model
{
    use ClearsResponseCache, HasTranslations;

    public const GROUPS = ['goals' => 'الأهداف', 'who' => 'من يحق له المشاركة', 'path' => 'المسار / الخطوات', 'criteria' => 'معايير التقييم', 'facts' => 'معلومات'];

    public const ICONS = ['flag' => 'علم', 'age' => 'شخص', 'ruler' => 'مسطرة', 'team' => 'فريق', 'pc' => 'حاسوب', 'pin' => 'موقع', 'build' => 'مبنى', 'cal' => 'تقويم', 'globe' => 'كرة أرضية'];

    protected $fillable = ['group', 'label', 'value', 'short', 'icon', 'sort_order'];

    /** @var list<string> */
    public array $translatable = ['label', 'value', 'short'];

    public function factable(): MorphTo
    {
        return $this->morphTo();
    }
}
