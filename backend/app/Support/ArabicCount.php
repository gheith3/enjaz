<?php

namespace App\Support;

/**
 * Counted nouns as the design writes them (js/main.js, js/achievement-enhance.js):
 * 0 "لا توجد إنجازات", 1 "إنجاز واحد", 2 "إنجازان", 3–10 "٣ إنجازات", 11+ "١١ إنجازًا".
 */
class ArabicCount
{
    /** @var array<string, array{0: string, 1: string, 2: string, 3: string, 4: string}> none, one, two, few (3-10), many (11+) */
    private const NOUNS = [
        'achievements' => ['لا توجد إنجازات', 'إنجاز واحد', 'إنجازان', 'إنجازات', 'إنجازًا'],
        'achievers' => ['لا يوجد منجزون', 'منجز واحد', 'منجزان', 'منجزين', 'منجزًا'],
        'minutes' => ['', 'دقيقة واحدة', 'دقيقتان', 'دقائق', 'دقيقة'],
        'articles' => ['لا توجد مقالات', 'مقال واحد', 'مقالان', 'مقالات', 'مقالًا'],
    ];

    public static function of(int $n, string $noun): string
    {
        [$none, $one, $two, $few, $many] = self::NOUNS[$noun];

        return match (true) {
            $n === 0 => $none,
            $n === 1 => $one,
            $n === 2 => $two,
            default => ArabicDate::digits($n).' '.($n <= 10 ? $few : $many),
        };
    }
}
