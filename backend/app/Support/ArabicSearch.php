<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

/**
 * Search that ignores hamza forms (also on ya and waw), ta marbuta / ha, alif maqsura / ya, diacritics and tatweel, like the
 * design's client-side search (js/main.js norm()). PostgreSQL only (the app's database).
 */
class ArabicSearch
{
    private const FROM = 'أإآٱةىئؤ';

    private const TO = 'ااااهييو';

    public static function normalize(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[\x{064B}-\x{0652}\x{0640}]/u', '', $text);
        $text = strtr($text, ['أ' => 'ا', 'إ' => 'ا', 'آ' => 'ا', 'ٱ' => 'ا', 'ة' => 'ه', 'ى' => 'ي', 'ئ' => 'ي', 'ؤ' => 'و']);

        return preg_replace('/\s+/u', ' ', $text);
    }

    /** A person's name without titles and the father's name ("د. جوخة بنت محمد الحارثية" = "جوخة الحارثية"), normalized. */
    public static function personKey(string $name): string
    {
        return self::normalize((string) preg_replace(['/^(د|م|أ)\.\s*/u', '/\s(بن|بنت)\s+\S+/u'], '', trim($name)));
    }

    /** SQL expression of a jsonb translation (e.g. title->>'ar') normalized the same way. */
    public static function expression(string $column, string $locale): string
    {
        $value = sprintf("coalesce(%s->>'%s', '')", $column, $locale);

        return sprintf("regexp_replace(translate(lower(%s), '%s', '%s'), '[ًٌٍَُِّْـ]', '', 'g')", $value, self::FROM, self::TO);
    }

    /**
     * Adds "any of these translatable columns contains the term" to a query (OR group).
     *
     * @param  list<string>  $columns
     */
    public static function where(Builder $query, array $columns, string $term): Builder
    {
        $needle = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], self::normalize($term)).'%';

        return $query->where(function (Builder $q) use ($columns, $needle) {
            foreach ($columns as $column) {
                foreach (['ar', 'en'] as $locale) {
                    $q->orWhereRaw(self::expression($q->qualifyColumn($column), $locale).' like ?', [$needle]);
                }
            }
        });
    }
}
