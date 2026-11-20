<?php

use App\Models\Award;
use App\Models\AwardWinner;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;
use Throwable;

/*
 * Completes the 13th edition (2026) of the culture award when 2026_11_20_160000 found winner records already on it (added
 * while it was still «الدورة الجارية», which hides them): the edition is closed (no longer current, culture field withheld,
 * the jury's note) and each of the two announced winners is added unless a winner with the same family name is there.
 */
return new class extends Migration
{
    private const WINNERS = [
        ['family' => 'الغاوي', 'branch' => 'arts', 'photo' => 'culture-2026-sami-alghawi', 'name' => 'سامي بن زين الغاوي', 'field' => 'الخط العربي', 'work' => null,
            'why' => 'اختارت لجنة التحكيم عمله من بين 41 عملًا في مجال الخط العربي، لما تضمّنه من دلالة فكرية تتناسب مع الوضع الراهن.'],
        ['family' => 'المنذري', 'branch' => 'lit', 'photo' => 'culture-2026-yahya-almandhari', 'name' => 'يحيى بن سلام المنذري', 'field' => 'القصة القصيرة', 'work' => 'مجموعة «وقت قصير للهلع»',
            'why' => 'منحته لجنة التحكيم الجائزة عن مجموعته القصصية «وقت قصير للهلع» من بين 113 عملًا في مجال القصة القصيرة، لما رصدته فيها من رؤية سردية ما بعد حداثية.'],
    ];

    public function up(): void
    {
        $award = Award::query()->where('slug', 'culture')->first() ?? Award::query()->where('kind', 'culture')->first();
        $edition = $award?->editions()->where(fn ($q) => $q->where('year', 'like', '%2026%')->orWhere('number', 13))->orderByDesc('is_current')->first();
        if (! $edition) {
            Log::warning('Culture award 2026: no 2026 edition found.');

            return;
        }
        $branches = $award->categories()->where('kind', 'branch')->pluck('id', 'key');
        $names = $edition->winners()->get()->map(fn (AwardWinner $w) => (string) $w->getTranslation('name', 'ar', false));
        foreach (self::WINNERS as $i => $w) {
            if ($names->contains(fn (string $name) => str_contains($name, $w['family']))) {
                continue;
            }
            $winner = $edition->winners()->create([
                'award_category_id' => $branches[$w['branch']] ?? null, 'name' => ['ar' => $w['name']], 'field' => ['ar' => $w['field']],
                'work' => $w['work'] ? ['ar' => $w['work']] : null, 'why' => ['ar' => $w['why']], 'gender' => 'm', 'sort_order' => $names->count() + $i + 1,
            ]);
            try {
                if (is_file($file = database_path('data/awards/'.$w['photo'].'.webp'))) {
                    $winner->addMedia($file)->preservingOriginal()->withCustomProperties(['alt' => $w['name']])->toMediaCollection('photo');
                }
            } catch (Throwable $e) {
                Log::warning('Culture award 2026: photo not attached for '.$w['name'].': '.$e->getMessage());
            }
        }
        $edition->forceFill(['is_current' => false, 'open_fields' => null, 'withheld' => $edition->withheld ?: ['culture']]);
        if (blank($edition->getTranslation('note', 'ar', false))) {
            $edition->setTranslation('note', 'ar', 'حُجبت جائزة مجال «دراسات الأسرة والطفولة في سلطنة عُمان» (فرع الثقافة) بتوصية لجنة التحكيم.');
        }
        $edition->save();
    }

    public function down(): void
    {
        // Data only; the dashboard owns these records afterwards.
    }
};
