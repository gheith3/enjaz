<?php

namespace App\Support\Site;

use App\Models\Achievement;
use App\Models\Achiever;
use Illuminate\Support\Collection;

/**
 * «إنجازاتي» on the achiever's page (design 9): the items of the page's #aj-data, read by the ported design scripts
 * (js/aj-bento.js tiles and reader, aj-pro.js stats bar, aj-split.js medal). Keys are the design's.
 */
class ProfileAchievements
{
    /**
     * @param  Collection<int, Achievement>  $achievements
     * @return list<array<string, mixed>>
     */
    public static function items(Collection $achievements, Achiever $achiever): array
    {
        return $achievements->map(fn (Achievement $achievement) => self::item($achievement, $achiever))->values()->all();
    }

    /**
     * The years and countries line of the stats card ("٢٠١٦ – ٢٠٢٣ · ٣ دول").
     *
     * @param  list<array<string, mixed>>  $items
     * @return array{from: ?int, to: ?int, countries: int}
     */
    public static function span(array $items): array
    {
        $years = collect($items)->map(fn (array $item) => (int) substr((string) $item['iso'], 0, 4))->filter();

        return [
            'from' => $years->min(),
            'to' => $years->max(),
            'countries' => collect($items)->pluck('country')->reject(fn ($country) => $country === '—')->unique()->count(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function item(Achievement $achievement, Achiever $achiever): array
    {
        $cover = $achievement->image('cover');

        return [
            'slug' => $achievement->slug,
            'title' => (string) $achievement->title,
            'short' => (string) $achievement->short_title,
            'rank' => $achievement->rank ?: 'award',
            'project' => (string) $achievement->project,
            'org' => (string) ($achievement->issuer ?: $achievement->organizations->first()?->name),
            'featured' => $achievement->is_profile_highlight ? 1 : 0,
            'stat' => (string) $achievement->stat,
            'scope' => $achievement->scope?->is_international ? 'international' : 'local',
            'iso' => self::iso($achievement),
            'date' => $achievement->dateLabel(),
            'country' => (string) $achievement->country ?: '—',
            'img' => $achievement->imageUrl('cover') ?? $achiever->imageUrl('portrait'),
            'pos' => $cover ? Achievement::focal($cover) : Achiever::focal($achiever->image('portrait')),
            'details' => self::details($achievement),
            'highlights' => collect($achievement->highlightItems())
                ->map(fn (array $item) => trim(($item['lead'] !== '' ? '<b>'.e($item['lead']).'</b> ' : '').e($item['text'])))
                ->values()->all(),
            'sources' => $achievement->sources
                ->filter(fn ($source) => filled($source->url))
                ->map(fn ($source) => ['org' => (string) $source->title, 'title' => (string) $source->subtitle, 'url' => $source->url])
                ->values()->all(),
            'href' => $achievement->url(),
        ];
    }

    /** The date as precise as the dashboard says: "2016", "2019-05" or "2019-05-21". */
    private static function iso(Achievement $achievement): string
    {
        return (string) $achievement->achieved_at?->format(match ($achievement->date_precision) {
            'year' => 'Y',
            'month' => 'Y-m',
            default => 'Y-m-d',
        });
    }

    /**
     * The story as the design's plain text: paragraphs separated by a blank line, list items as "• " lines.
     */
    private static function details(Achievement $achievement): string
    {
        $blocks = collect($achievement->bodyParagraphs())->map(function (string $html): string {
            if (preg_match('#^<(ul|ol)\b#u', $html)) {
                preg_match_all('#<li\b[^>]*>(.*?)</li>#su', $html, $items);

                return collect($items[1])->map(fn (string $item) => '• '.self::text($item))->implode("\n");
            }

            return self::text($html);
        })->filter()->values();

        return $blocks->isNotEmpty() ? $blocks->implode("\n\n") : self::text((string) $achievement->excerpt);
    }

    private static function text(string $html): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    }
}
