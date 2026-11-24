<?php

namespace App\Support\ProfileReviews;

use App\Models\Achiever;
use App\Models\Field;
use App\Models\ProfileReview;
use Illuminate\Support\Collection;

/**
 * Data of the dashboard «مراجعات المنجزين»: every achiever with the stage of their latest review
 * (pending: not reviewed yet, reviewed: waiting for approval, published), grouped by field.
 */
class ReviewBoard
{
    public const STAGES = [
        'pending' => 'لم يراجع',
        'reviewed' => 'راجع · بانتظار اعتمادك',
        'published' => 'نُشرت مراجعته',
    ];

    /** @return Collection<int, array{achiever: Achiever, review: ?ProfileReview, stage: string}> */
    public static function people(?string $field = null): Collection
    {
        $reviews = ProfileReview::query()->orderByDesc('id')->get()->unique('achiever_id')->keyBy('achiever_id');

        return Achiever::query()->orderBy('sort_order')->orderBy('id')
            ->when($field === 'none', fn ($q) => $q->whereNull('field_id'))
            ->when($field !== null && $field !== 'none', fn ($q) => $q->where('field_id', (int) $field))
            ->get()
            ->map(fn (Achiever $achiever) => [
                'achiever' => $achiever,
                'review' => $review = $reviews->get($achiever->id),
                'stage' => $review?->stage() ?? 'pending',
            ]);
    }

    /** @return array<string, int> stage => count */
    public static function counts(Collection $people): array
    {
        return collect(self::STAGES)->map(fn ($label, $stage) => $people->where('stage', $stage)->count())->all();
    }

    /** @return Collection<int, array{key: string, name: string, people: Collection}> */
    public static function fields(): Collection
    {
        $people = self::people();
        $fields = Field::query()->orderBy('sort_order')->get()->map(fn (Field $field) => [
            'key' => (string) $field->id,
            'name' => (string) $field->getTranslation('name', 'ar', false),
            'people' => $people->filter(fn ($p) => $p['achiever']->field_id === $field->id)->values(),
        ]);
        $none = $people->filter(fn ($p) => $p['achiever']->field_id === null)->values();

        return $none->isEmpty() ? $fields : $fields->push(['key' => 'none', 'name' => 'بلا مجال بعد', 'people' => $none]);
    }
}
