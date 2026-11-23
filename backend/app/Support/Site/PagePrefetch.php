<?php

namespace App\Support\Site;

use App\Models\Award;
use Illuminate\Support\Facades\Vite;
use Throwable;

/**
 * تصاميم ٢٠٧٠ — the merged stylesheet of each site section, so the browser can fetch it before the visitor opens the
 * section (resources/site/js/core/prefetch.js: when idle, and on hover/touch of a link). The next page then only waits
 * for its HTML (itself prefetched by the speculation rules in the layout).
 */
class PagePrefetch
{
    /** Exact paths of the sections and the styles key their page uses (config/site.php "styles"). */
    private const PAGES = [
        '/' => 'home',
        '/achievements' => 'achievements',
        '/achievers' => 'achievers',
        '/inspiring' => 'inspiring',
        '/awards' => 'awards',
        '/organizations' => 'organizations',
        '/universities' => 'universities',
        '/lab' => 'lab',
        '/magazine' => 'magazine',
        '/writers' => 'writers',
        '/news' => 'news',
        '/about' => 'about',
        '/contact' => 'contact',
    ];

    /** Path prefixes of the detail pages that share one design. */
    private const PREFIXES = [
        '/achievements/' => 'achievement',
        '/achievers/' => 'achiever',
        '/magazine/' => 'article',
    ];

    /** Award designs by kind (resources/views/site/awards/<kind>.blade.php). */
    private const AWARD_KINDS = [
        'architecture' => 'award-architecture',
        'culture' => 'award-culture',
        'volunteer' => 'award-volunteer',
    ];

    /**
     * @return array{pages: array<string, string>, prefixes: array<string, string>} path => stylesheet URL
     */
    public static function map(): array
    {
        try {
            $pages = self::PAGES;
            foreach (Award::query()->pluck('kind', 'slug') as $slug => $kind) {
                if (isset(self::AWARD_KINDS[$kind])) {
                    $pages['/awards/'.$slug] = self::AWARD_KINDS[$kind];
                }
            }

            return [
                'pages' => array_map(self::url(...), $pages),
                'prefixes' => array_map(self::url(...), self::PREFIXES),
            ];
        } catch (Throwable) {
            return ['pages' => [], 'prefixes' => []];
        }
    }

    private static function url(string $key): string
    {
        return Vite::asset(CssBundles::path($key));
    }
}
