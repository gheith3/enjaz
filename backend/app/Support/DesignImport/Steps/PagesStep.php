<?php

namespace App\Support\DesignImport\Steps;

use App\Models\Page;
use App\Support\DesignImport\DesignMap;
use App\Support\DesignImport\DesignSource;
use App\Support\DesignImport\ImportReport;

/**
 * Title, meta description and sharing image of each fixed page, from the design page's <head>.
 */
class PagesStep implements ImportStep
{
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
            if (! $page || ($page->meta_description && ! $force)) {
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
}
