<?php

namespace App\Models;

use App\Models\Concerns\ClearsResponseCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

/**
 * A station of an achiever's "مسيرتي" timeline (separate from achievements).
 */
class AchieverJourneyItem extends Model
{
    use ClearsResponseCache, HasTranslations;

    /** Icons of the design's timeline (css/profile-journey.css [data-icon]). */
    public const ICONS = [
        'trophy' => 'كأس', 'medal' => 'وسام', 'star' => 'نجمة', 'ball' => 'كرة', 'plane' => 'سفر', 'book' => 'كتاب',
        'feather' => 'قلم', 'doc' => 'وثيقة', 'cap' => 'تخرج', 'uni' => 'جامعة', 'museum' => 'متحف',
    ];

    protected $fillable = ['achiever_id', 'year', 'title', 'icon', 'sort_order'];

    /** @var list<string> */
    public array $translatable = ['title'];

    public function achiever(): BelongsTo
    {
        return $this->belongsTo(Achiever::class);
    }
}
