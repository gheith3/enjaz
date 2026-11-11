<?php

namespace App\Support\DesignImport\Steps;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Page;
use App\Support\DesignImport\DesignSource;
use App\Support\DesignImport\ImportReport;

/**
 * The news section: news.html (category title and intro) and news-content.json (as database/data/design/news.json).
 * The design marks these items as fictional ("تخيّلي"), so they are imported as drafts.
 */
class NewsStep implements ImportStep
{
    public function key(): string
    {
        return 'news';
    }

    public function description(): string
    {
        return 'News section';
    }

    public function run(DesignSource $design, ImportReport $report, bool $force): void
    {
        $html = $design->page('news.html');
        $text = fn (string $sel) => $html->filter($sel)->count() ? DesignSource::text($html->filter($sel)->first()) : '';
        $items = $design->data('news.json');

        $categories = [];
        foreach (collect($items)->pluck('category')->unique()->values() as $i => $slug) {
            $category = NewsCategory::query()->firstOrNew(['slug' => $slug]);
            if (! $category->exists || $force) {
                // news.html shows the "science" category; its title and intro are the page heading.
                $category->fill(['name' => ['ar' => $text('#news-page-title')], 'intro' => ['ar' => $text('.topic-news-intro')], 'sort_order' => $i + 1])->save();
            }
            $categories[$slug] = $category->id;
        }

        $page = Page::forKey('news');
        if (! $page->meta_description || $force) {
            $page->fill(['meta_description' => ['ar' => (string) $html->filter('meta[name="description"]')->attr('content', '')]])->save();
        }

        $count = 0;
        foreach ($items as $i => $n) {
            $news = News::withTrashed()->firstOrNew(['slug' => $n['id']]);
            if ($news->exists && ! $force) {
                continue;
            }
            $news->fill([
                'news_category_id' => $categories[$n['category']] ?? null, 'kind' => 'news', 'tag' => ['ar' => $n['tag']],
                'title' => ['ar' => $n['title']], 'summary' => ['ar' => $n['summary']],
                'body' => ['ar' => collect($n['body'])->map(fn ($p) => '<p>'.e($p).'</p>')->implode("\n")],
                'is_published' => empty($n['fictional']), 'sort_order' => $i + 1,
            ])->save();
            if ($news->trashed()) {
                $news->restore();
            }
            if ($force || ! $news->hasMedia('cover')) {
                $news->clearMediaCollection('cover');
                try {
                    $news->addMedia($design->file('assets/img/'.$n['image']))->preservingOriginal()
                        ->withCustomProperties(['alt' => $n['alt'] ?? ''])->toMediaCollection('cover');
                } catch (\RuntimeException) {
                    $report->note('Image not found in the design: assets/img/'.$n['image']);
                }
            }
            $count++;
        }
        $report->done("news: $count items imported into ".count($categories).' categories');
        $report->note('News section: the design marks its news as fictional ("تخيّلي"); imported as drafts. Publish real news from الأخبار in the dashboard.');
    }
}
