<?php

namespace App\Support\DesignImport\Steps;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Issue;
use App\Models\Page;
use App\Models\Redirect;
use App\Models\Writer;
use App\Support\DesignImport\DesignSource;
use App\Support\DesignImport\ImportReport;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Symfony\Component\DomCrawler\Crawler;

/**
 * مجلة إنجازات from articles.html (hero, issue, lead, quote, rows, categories), article.html (the full text of the
 * issue's lead article) and writers.html (writers, their articles and dates). The design says its articles and writers
 * are for testing ("مقالات تجريبية"), so writers, articles and the issue are imported as drafts.
 */
class MagazineStep implements ImportStep
{
    private const CATEGORY_SLUGS = ['رأي' => 'opinion', 'قصص ملهمة' => 'inspiring', 'علوم' => 'science', 'رياضة' => 'sport', 'ثقافة' => 'culture'];

    private const MONTHS = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];

    private const LEAD_SLUG = 'after-the-applause';

    private DesignSource $design;

    private ImportReport $report;

    private bool $force;

    public function key(): string
    {
        return 'magazine';
    }

    public function description(): string
    {
        return 'Magazine: writers, issue and articles';
    }

    public function run(DesignSource $design, ImportReport $report, bool $force): void
    {
        [$this->design, $this->report, $this->force] = [$design, $report, $force];
        $list = $design->page('articles.html');
        $wpage = $design->page('writers.html');

        // Categories (the tabs).
        $categories = [];
        $list->filter('.mag-tabs [data-tab]')->each(function (Crawler $b, int $i) use (&$categories) {
            $name = (string) $b->attr('data-tab');
            if ($name === '*') {
                return;
            }
            $slug = self::CATEGORY_SLUGS[$name] ?? Str::slug($name) ?: 'c'.$i;
            $categories[$name] = ArticleCategory::query()->firstOrCreate(['slug' => $slug], ['name' => ['ar' => $name], 'sort_order' => $i])->id;
        });

        // Writers and the date / minutes of every article they wrote.
        $writers = [];
        $dates = [];
        $featured = DesignSource::text($wpage->filter('.wr-feature h2')->first());
        $wpage->filter('.wr-card')->each(function (Crawler $card, int $i) use (&$writers, &$dates, $categories, $featured, $wpage) {
            $name = DesignSource::text($card->filter('h3'));
            $writer = Writer::withTrashed()->firstOrNew(['slug' => (string) $card->attr('id')]);
            if (! $writer->exists || $this->force) {
                $isFeatured = $name === $featured;
                $writer->fill([
                    'name' => ['ar' => $name], 'role' => ['ar' => DesignSource::text($card->filter('.wr-role'))],
                    'article_category_id' => $categories[(string) $card->attr('data-cat')] ?? null,
                    'quote' => $isFeatured ? ['ar' => trim(DesignSource::text($wpage->filter('.wr-feature blockquote')), '«» ')] : null,
                    'is_featured' => $isFeatured, 'is_published' => false, 'sort_order' => $i + 1,
                ])->save();
                $this->attach($writer, 'photo', $card->filter('.wr-ph img')->attr('src'), $name);
            }
            $writers[$name] = $writer->id;
            $card->filter('.wr-arts li')->each(function (Crawler $li) use (&$dates, $name) {
                $dates[DesignSource::text($li->filter('.wa-t'))] = [(string) $li->attr('data-date'), $this->number(DesignSource::text($li->filter('.wa-m'))), $name];
            });
        });

        $issue = $this->issue($list);

        // The issue's lead article, with its full text from article.html.
        $leadCard = $list->filter('.mag-lead');
        $leadTitle = DesignSource::text($leadCard->filter('h2'));
        $lead = $this->article(self::LEAD_SLUG, $leadTitle, $leadCard, $categories, $writers, $dates, $issue);
        if ($lead) {
            $this->fullText($lead, $design->page('article.html'));
        }
        $issue?->forceFill(['lead_article_id' => Article::query()->where('slug', self::LEAD_SLUG)->value('id')])->save();

        // Rows of articles.html, then the articles that only appear on writers.html.
        $done = [$leadTitle => true];
        $list->filter('#mag-all .mag-row')->each(function (Crawler $row) use (&$done, $categories, $writers, $dates, $issue) {
            $title = DesignSource::text($row->filter('h3'));
            $done[$title] = true;
            $this->article(Str::after((string) $row->attr('id'), 'a-'), $title, $row, $categories, $writers, $dates, $issue);
        });
        $i = 0;
        foreach ($dates as $title => [$date, $minutes, $writer]) {
            if (! isset($done[$title])) {
                $this->article('article-'.(++$i), $title, null, $categories, $writers, $dates, $issue);
            }
        }

        // Writers' bios (article.html shows the lead writer's).
        $bio = DesignSource::text($design->page('article.html')->filter('.ar-author p'));
        $leadWriter = Writer::query()->find($writers[DesignSource::text($design->page('article.html')->filter('.ar-author b'))] ?? 0);
        if ($leadWriter && (! $leadWriter->bio || $this->force)) {
            $leadWriter->forceFill(['bio' => ['ar' => $bio]])->save();
        }

        $this->pages($list, $wpage);
        Redirect::query()->updateOrCreate(['from_path' => Redirect::normalize('article.html')], ['to_path' => '/magazine', 'status_code' => 301]);

        $report->done('magazine: '.count($writers).' writers, '.Article::query()->count().' articles, '.count($categories).' categories (drafts)');
        $report->note('Magazine: the design marks its articles and writers as test content ("تجريبية"); writers, articles and the issue are imported as drafts. Publish them from the dashboard (المقالات، الكتّاب، الأعداد).');
    }

    private function issue(Crawler $list): ?Issue
    {
        $spans = $list->filter('.mag-mast .mm-line span');
        if (! $spans->count()) {
            return null;
        }
        $number = $this->number(DesignSource::text($spans->eq(0)));
        $issue = Issue::query()->firstOrNew(['number' => $number]);
        if (! $issue->exists || $this->force) {
            $issue->fill([
                'issued_at' => $this->monthDate(DesignSource::text($spans->eq(1))),
                'tagline' => ['ar' => DesignSource::text($list->filter('.mm-tag'))],
                'quote' => ['ar' => DesignSource::text($list->filter('.mag-quote blockquote'))],
                'quote_cite' => ['ar' => ltrim(DesignSource::text($list->filter('.mag-quote cite')), '—– ')],
                'is_published' => false,
            ])->save();
        }

        return $issue;
    }

    /**
     * @param  array<string, int>  $categories
     * @param  array<string, int>  $writers
     * @param  array<string, array{0: string, 1: int, 2: string}>  $dates
     */
    private function article(string $slug, string $title, ?Crawler $card, array $categories, array $writers, array $dates, ?Issue $issue): ?Article
    {
        $article = Article::withTrashed()->firstOrNew(['slug' => $slug]);
        if ($article->exists && ! $this->force) {
            return null;
        }
        [$date, $minutes, $writer] = $dates[$title] ?? [null, 1, null];
        $category = $card ? (string) $card->attr('data-cat') : null;
        if (! $category && $writer) {
            $category = Writer::query()->find($writers[$writer] ?? 0)?->category?->getTranslation('name', 'ar');
        }
        $article->fill([
            'title' => ['ar' => $title], 'dek' => $card && $card->filter('.ex')->count() ? ['ar' => DesignSource::text($card->filter('.ex'))] : null,
            'article_category_id' => $categories[$category] ?? null, 'writer_id' => $writers[$writer] ?? null, 'issue_id' => $issue?->id,
            'read_minutes' => $minutes ?: 1, 'published_at' => $date ? Carbon::parse($date) : null, 'is_published' => false,
        ])->save();
        if ($article->trashed()) {
            $article->restore();
        }
        if ($card && $card->filter('img.gold-ph')->count()) {
            $img = $card->filter('img.gold-ph');
            $this->attach($article, 'cover', $img->attr('src'), $img->attr('alt'));
        }

        return $article;
    }

    /** article.html: lead paragraph, body (paragraphs and headings), pull quote, summary box, tags, cover caption. */
    private function fullText(Article $article, Crawler $html): void
    {
        $body = $html->filter('.ar-body')->children()->each(function (Crawler $n) {
            $tag = $n->nodeName();
            $class = (string) $n->attr('class');

            return match (true) {
                $tag === 'h2' => '<h2>'.e(DesignSource::text($n)).'</h2>',
                $tag === 'p' && ! str_contains($class, 'ar-lead') => '<p>'.e(DesignSource::text($n)).'</p>',
                default => '',
            };
        });
        $pull = $html->filter('.ar-pull');
        $article->fill([
            'lead' => ['ar' => DesignSource::text($html->filter('.ar-lead'))],
            'body' => ['ar' => implode("\n", array_filter($body))],
            'dek' => ['ar' => DesignSource::text($html->filter('.ar-dek'))],
            'pull_quote' => $pull->count() ? ['ar' => trim(str_replace('”', '', DesignSource::text($pull)))] : null,
            'box_title' => ['ar' => DesignSource::text($html->filter('.ar-box-t'))],
            'box_items' => ['ar' => $html->filter('.ar-box li')->each(fn (Crawler $li) => DesignSource::text($li))],
            'tags' => ['ar' => $html->filter('.ar-tags a')->each(fn (Crawler $a) => ltrim(DesignSource::text($a), '#'))],
            'cover_caption' => ['ar' => DesignSource::text($html->filter('.ar-cover figcaption'))],
        ])->save();
    }

    /** Hero and small texts of articles.html and writers.html. */
    private function pages(Crawler $list, Crawler $wpage): void
    {
        $mag = Page::forKey('magazine');
        if (! $mag->hero_title || $this->force) {
            $note = $list->filter('.mag-hero-note b');
            $mag->fill([
                'meta_description' => ['ar' => (string) $list->filter('meta[name="description"]')->attr('content', '')],
                'hero_eyebrow' => ['ar' => DesignSource::text($list->filter('.mag-hero-kicker'))],
                'hero_title' => ['ar' => DesignSource::text($list->filter('#mag-hero-title'))],
                'hero_lead' => ['ar' => DesignSource::text($list->filter('.mag-hero-intro'))],
                'hero_cta_label' => ['ar' => trim(str_replace('←', '', DesignSource::text($list->filter('.mag-hero-link'))))],
            ]);
            $mag->extra = array_map(fn ($v) => ['ar' => $v], array_filter([
                'note_kicker' => DesignSource::text($list->filter('.mag-hero-note span')),
                'note_text' => $note->count() ? trim(implode('|', array_map('trim', preg_split('#<br\s*/?>#', $note->html())))) : '',
                'cta_title' => DesignSource::text($list->filter('.mag-cta b')), 'cta_text' => DesignSource::text($list->filter('.mag-cta small')),
            ])) + ($mag->extra ?? []);
            $mag->save();
        }

        $wr = Page::forKey('writers');
        if (! $wr->hero_title || $this->force) {
            $h1 = $wpage->filter('.wr-hero h1');
            $span = DesignSource::text($h1->filter('span'));
            $wr->fill([
                'meta_description' => ['ar' => (string) $wpage->filter('meta[name="description"]')->attr('content', '')],
                'hero_title' => ['ar' => trim(str_replace($span, '', DesignSource::text($h1))).'|'.$span],
                'hero_lead' => ['ar' => DesignSource::text($wpage->filter('.wr-hero p'))],
            ]);
            $wr->extra = array_map(fn ($v) => ['ar' => $v], array_filter([
                'kicker' => DesignSource::text($wpage->filter('.wr-hero .mm-line span')->first()),
                'cta_title' => DesignSource::text($wpage->filter('.mag-cta b')), 'cta_text' => DesignSource::text($wpage->filter('.mag-cta small')),
            ])) + ($wr->extra ?? []);
            $wr->save();
        }
    }

    /** First number in a text, Arabic or Latin digits ("العدد ١٢" -> 12). */
    private function number(string $text): int
    {
        return (int) preg_replace('/\D/', '', strtr($text, array_combine(mb_str_split('٠١٢٣٤٥٦٧٨٩'), str_split('0123456789'))));
    }

    /** "نوفمبر ٢٠٢٦" -> 2026-11-01 */
    private function monthDate(string $text): ?string
    {
        $year = $this->number($text);
        foreach (self::MONTHS as $i => $name) {
            if (str_contains($text, $name) && $year) {
                return Carbon::create($year, $i + 1, 1)->toDateString();
            }
        }

        return null;
    }

    private function attach(HasMedia $model, string $collection, ?string $src, ?string $alt = null): void
    {
        if (! $src || ($model->hasMedia($collection) && ! $this->force)) {
            return;
        }
        $path = ltrim(strtok($src, '?'), '/');
        try {
            $file = $this->design->file($path);
        } catch (\RuntimeException) {
            $this->report->note("Image not found in the design: $path");

            return;
        }
        $model->clearMediaCollection($collection);
        $model->addMedia($file)->preservingOriginal()->withCustomProperties(array_filter(['alt' => $alt]))->toMediaCollection($collection);
    }
}
