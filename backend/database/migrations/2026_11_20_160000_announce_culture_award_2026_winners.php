<?php

use App\Models\Award;
use Illuminate\Database\Migrations\Migration;

/*
 * Sultan Qaboos Award for Culture, Arts and Literature, 13th edition (2026, Omani edition): the winners were announced, so the
 * edition is no longer «الدورة الجارية». Arts (Arabic calligraphy): Sami bin Zain Al-Ghawi; Literature (short story): Yahya bin
 * Salam Al-Mandhari for «وقت قصير للهلع»; the culture branch field (family and childhood studies) was withheld on the jury's
 * recommendation. Photos from database/data/awards. Runs only while the edition has no winners, so dashboard edits are kept.
 */
return new class extends Migration
{
    private const WINNERS = [
        ['branch' => 'arts', 'photo' => 'culture-2026-sami-alghawi', 'name' => 'سامي بن زين الغاوي', 'field' => 'الخط العربي', 'work' => null,
            'why' => 'اختارت لجنة التحكيم عمله من بين 41 عملًا في مجال الخط العربي، لما تضمّنه من دلالة فكرية تتناسب مع الوضع الراهن.'],
        ['branch' => 'lit', 'photo' => 'culture-2026-yahya-almandhari', 'name' => 'يحيى بن سلام المنذري', 'field' => 'القصة القصيرة', 'work' => 'مجموعة «وقت قصير للهلع»',
            'why' => 'منحته لجنة التحكيم الجائزة عن مجموعته القصصية «وقت قصير للهلع» من بين 113 عملًا في مجال القصة القصيرة، لما رصدته فيها من رؤية سردية ما بعد حداثية.'],
    ];

    public function up(): void
    {
        $award = Award::query()->where('slug', 'culture')->first() ?? Award::query()->where('kind', 'culture')->first();
        $edition = $award?->editions()->where('year', '2026')->first();
        if (! $edition || $edition->winners()->exists()) {
            return;
        }
        $edition->forceFill([
            'is_current' => false, 'open_fields' => null, 'withheld' => ['culture'],
            'note' => ['ar' => 'حُجبت جائزة مجال «دراسات الأسرة والطفولة في سلطنة عُمان» (فرع الثقافة) بتوصية لجنة التحكيم.'],
        ])->save();
        $branches = $award->categories()->where('kind', 'branch')->pluck('id', 'key');
        foreach (self::WINNERS as $i => $w) {
            $winner = $edition->winners()->create([
                'award_category_id' => $branches[$w['branch']] ?? null, 'name' => ['ar' => $w['name']], 'field' => ['ar' => $w['field']],
                'work' => $w['work'] ? ['ar' => $w['work']] : null, 'why' => ['ar' => $w['why']], 'gender' => 'm', 'sort_order' => $i + 1,
            ]);
            if (is_file($file = database_path('data/awards/'.$w['photo'].'.webp'))) {
                $winner->addMedia($file)->preservingOriginal()->withCustomProperties(['alt' => $w['name']])->toMediaCollection('photo');
            }
        }
    }

    public function down(): void
    {
        // Data only; the dashboard owns these records afterwards.
    }
};
