<?php

namespace App\Support\DesignImport\Steps;

use App\Models\MenuItem;
use App\Support\DesignImport\DesignMap;
use App\Support\DesignImport\DesignSource;
use App\Support\DesignImport\ImportReport;
use Symfony\Component\DomCrawler\Crawler;

/**
 * The full-screen menu and the two footer columns (index.html), with the desktop bar's short labels
 * (design js/desktop.js `keep` and `short`).
 */
class MenusStep implements ImportStep
{
    public function key(): string
    {
        return 'menus';
    }

    public function description(): string
    {
        return 'Header menu, desktop bar and footer links';
    }

    public function run(DesignSource $design, ImportReport $report, bool $force): void
    {
        if (MenuItem::query()->exists() && ! $force) {
            $report->done('menus: kept (already in the database)');

            return;
        }

        MenuItem::query()->delete();
        [$keep, $short] = $this->desktopBar($design->read('js/desktop.js'));
        $home = $design->page('index.html');

        $home->filter('#menu nav a')->each(function (Crawler $a, int $i) use ($keep, $short, $report) {
            $file = $a->attr('href');
            MenuItem::create([
                'location' => 'header',
                'label' => ['ar' => DesignSource::text($a)],
                'label_short' => isset($short[$file]) ? ['ar' => $short[$file]] : null,
                'url' => $this->url($file, $report),
                'show_in_desktop_nav' => in_array($file, $keep, true),
                'sort_order' => $i + 1,
            ]);
        });

        $home->filter('.footer-link-columns ul')->each(function (Crawler $ul, int $column) use ($report) {
            $ul->filter('a')->each(fn (Crawler $a, int $i) => MenuItem::create([
                'location' => 'footer_'.($column + 1),
                'label' => ['ar' => DesignSource::text($a)],
                'url' => $this->url($a->attr('href'), $report),
                'sort_order' => $i + 1,
            ]));
        });

        $report->done('menus: '.MenuItem::query()->where('location', 'header')->count().' header, '
            .MenuItem::query()->where('location', '!=', 'header')->count().' footer links');
    }

    private function url(string $href, ImportReport $report): string
    {
        $path = DesignMap::path($href);
        if ($path === null) {
            $report->note("Menu link to $href has no new address yet; kept as /");
        }

        return $path ?? '/';
    }

    /**
     * @return array{0: list<string>, 1: array<string, string>}
     */
    private function desktopBar(string $js): array
    {
        preg_match("/var keep = \[([^\]]*)\]/", $js, $keep);
        preg_match("/var short = \{([^}]*)\}/", $js, $short);
        preg_match_all("/'([^']+)'/", $keep[1] ?? '', $keepFiles);
        preg_match_all("/'([^']+)':\s*'([^']+)'/", $short[1] ?? '', $pairs, PREG_SET_ORDER);

        return [$keepFiles[1], collect($pairs)->mapWithKeys(fn ($p) => [$p[1] => $p[2]])->all()];
    }
}
