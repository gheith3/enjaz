<?php

namespace App\Support\DesignImport\Steps;

use App\Models\Page;
use App\Support\DesignImport\DesignMap;
use App\Support\DesignImport\DesignSource;
use App\Support\DesignImport\ImportReport;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Title, meta description and sharing image of each fixed page, from the design page's <head>.
 */
class PagesStep implements ImportStep
{
    /** Hero title / subtitle selectors of pages whose design hero shows them. */
    private const HEROES = [
        'achievements' => ['.ah2-title', '.ah2-sub'],
        'achievers' => ['.ap-title', '.ap-sub'],
    ];

    public function key(): string
    {
        return 'pages';
    }

    public function description(): string
    {
        return 'Page titles, descriptions and sharing images';
    }

    public function run(DesignSource $design, ImportReport $report, bool $force): void
    {
        Page::syncFromConfig();
        $updated = 0;

        foreach (DesignMap::PAGE_KEYS as $key => $file) {
            $page = Page::query()->where('key', $key)->first();
            $html = $design->page($file);
            if (! $page) {
                continue;
            }
            $this->hero($page, $html, $key, $force);
            if ($page->meta_description && ! $force) {
                continue;
            }

            $title = trim(explode('|', $html->filter('title')->text(''))[0]);
            $page->setTranslation('title', 'ar', $title ?: $page->title);
            $page->setTranslation('meta_description', 'ar', (string) $html->filter('meta[name="description"]')->attr('content'));
            $page->save();

            $image = (string) $html->filter('meta[property="og:image"]')->attr('content');
            if ($image && ! str_ends_with($image, 'og-default.jpg') && ($force || ! $page->hasMedia('og_image'))) {
                $page->addMedia($design->file($image))->preservingOriginal()->toMediaCollection('og_image');
            }
            $updated++;
        }

        $report->done("pages: $updated updated");
    }

    private function hero(Page $page, Crawler $html, string $key, bool $force): void
    {
        [$title, $lead] = self::HEROES[$key] ?? [null, null];
        if (! $title || ($page->hero_lead && ! $force)) {
            return;
        }
        $heading = $html->filter($title);
        $sub = $html->filter($lead);
        $page->setTranslation('hero_title', 'ar', $heading->count() ? DesignSource::text($heading) : '');
        $page->setTranslation('hero_lead', 'ar', $sub->count() ? trim(strip_tags(preg_replace('#<br\s*/?>#i', "\n", $sub->html()))) : '');
        $page->save();
    }
}
