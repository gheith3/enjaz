<?php

namespace App\Support\DesignImport\Steps;

use App\Models\Achiever;
use App\Models\LabItem;
use App\Models\Organization;
use App\Models\Page;
use App\Models\Redirect;
use App\Support\ArabicSearch;
use App\Support\DesignImport\DesignSource;
use App\Support\DesignImport\ImportReport;
use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\DomCrawler\Crawler;

/**
 * The lab: inventions and publications from js/lab-data.js (as database/data/design/lab.json) and the texts of
 * science.html (hero, section titles, call to action). The items are real (with sources), so they are published.
 */
class LabStep implements ImportStep
{
    private const MONTHS = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];

    /** Filter names of the exhibitions (design lab-list.js groups). */
    private const EVENTS = ['ITEX' => 'ITEX ماليزيا', 'Geneva' => 'جنيف'];

    private DesignSource $design;

    private ImportReport $report;

    private bool $force;

    public function key(): string
    {
        return 'lab';
    }

    public function description(): string
    {
        return 'Lab: inventions and publications';
    }

    public function run(DesignSource $design, ImportReport $report, bool $force): void
    {
        [$this->design, $this->report, $this->force] = [$design, $report, $force];
        $data = $design->data('lab.json');
        $orgs = Organization::query()->get(['id', 'name'])->mapWithKeys(fn (Organization $o) => [ArabicSearch::normalize($o->getTranslation('name', 'ar')) => $o->id]);
        $people = Achiever::query()->get(['id', 'name'])->mapWithKeys(fn (Achiever $a) => [ArabicSearch::personKey($a->getTranslation('name', 'ar')) => $a->id]);

        $created = 0;
        foreach ($data['inv'] as $i => $x) {
            $orgId = isset($x['org']) ? ($orgs[ArabicSearch::normalize($x['org'])] ?? null) : null;
            $item = $this->item($x['id'], [
                'kind' => 'invention', 'title' => ['ar' => $x['title']], 'short' => $this->t($x['short'] ?? null),
                'lead' => ['ar' => $x['lead']], 'who' => ['ar' => $x['who']], 'field' => $this->t($x['field'] ?? null),
                'organization_id' => $orgId, 'org_label' => $orgId ? null : $this->t($x['org'] ?? null),
                'achiever_id' => $people[ArabicSearch::personKey($x['who'])] ?? null,
                'medal' => $x['medal'], 'event' => self::EVENTS[$x['event']] ?? $x['event'], 'event_label' => ['ar' => $x['eventLabel']],
                'place' => $this->t($x['place'] ?? null), 'happened_at' => $this->date($x['date'], $x['year']), 'date_precision' => 'month',
                'credit_text' => $x['credit'][0] ?? null, 'credit_url' => $x['credit'][1] ?? null, 'sort_order' => $i + 1,
            ]);
            if (! $item) {
                continue;
            }
            $created++;
            $this->attach($item, 'cover', $x['img'], alt: $x['imgAlt'] ?? null);
            $item->forceFill(['body' => ['ar' => $this->body($item, $x['body'])]])->save();
            foreach ($x['facts'] as $j => [$label, $value]) {
                $item->facts()->create(['group' => 'facts', 'label' => ['ar' => $label], 'value' => ['ar' => $value], 'sort_order' => $j + 1]);
            }
            foreach ($x['sources'] as $j => $s) {
                $item->sources()->create(['title' => ['ar' => $s[0]], 'url' => $s[1] ?? null, 'sort_order' => $j + 1]);
            }
        }
        foreach ($data['pubs'] as $i => $x) {
            $item = $this->item($x['id'], [
                'kind' => 'publication', 'title' => ['ar' => $x['title']], 'who' => ['ar' => $x['lead']], 'authors' => ['ar' => $x['authors']],
                'journal' => $this->t($x['journal'] ?? null), 'pub_type' => $this->t($x['type'] ?? null), 'field' => ['ar' => $x['field']],
                'finding_value' => ['ar' => $x['finding'][0]], 'finding_text' => ['ar' => $x['finding'][1]], 'summary' => ['ar' => $x['summary']],
                'achiever_id' => $people[ArabicSearch::personKey($x['lead'])] ?? null,
                'happened_at' => $this->date($x['date'], $x['year']), 'date_precision' => 'month', 'cover_is_illustrative' => true, 'sort_order' => $i + 1,
            ]);
            if (! $item) {
                continue;
            }
            $created++;
            $this->attach($item, 'cover', 'assets/img/lab/pub-'.$x['id'].'.svg');
        }

        $this->page();
        Redirect::query()->updateOrCreate(['from_path' => Redirect::normalize('lab-item.html')], ['to_path' => '/lab', 'status_code' => 301]);
        $report->done('lab: '.$created.' items imported; '.LabItem::query()->count().' in total');
    }

    /** @param  array<string, mixed>  $attributes */
    private function item(string $slug, array $attributes): ?LabItem
    {
        $item = LabItem::withTrashed()->firstOrNew(['slug' => $slug]);
        if ($item->exists && ! $this->force) {
            return null;
        }
        $item->fill($attributes + ['is_published' => true])->save();
        if ($item->trashed()) {
            $item->restore();
        }
        $item->facts()->delete();
        $item->sources()->delete();

        return $item;
    }

    /** Story blocks of the design ({h}, {p}, {ul}, {q, by}, {img: [src, caption]}) as the HTML the page renders. */
    private function body(LabItem $item, array $blocks): string
    {
        if ($this->force) {
            $item->clearMediaCollection('images');
        }

        return collect($blocks)->map(function (array $b) use ($item) {
            $e = fn ($s) => e((string) $s);

            return match (true) {
                isset($b['h']) => '<h2>'.$e($b['h']).'</h2>',
                isset($b['p']) => '<p>'.$e($b['p']).'</p>',
                isset($b['ul']) => '<ul>'.collect($b['ul'])->map(fn ($li) => '<li>'.$e($li).'</li>')->implode('').'</ul>',
                isset($b['q']) => '<blockquote><p>'.$e($b['q']).'</p><cite>'.$e($b['by'] ?? '').'</cite></blockquote>',
                isset($b['img']) => ($m = $this->attach($item, 'images', $b['img'][0], alt: $b['img'][1] ?? null, always: true))
                    ? '<figure><img src="'.$e($m->getUrl()).'" alt="'.$e($b['img'][1] ?? '').'" loading="lazy"><figcaption>'.$e($b['img'][1] ?? '').'</figcaption></figure>' : '',
                default => '',
            };
        })->implode("\n");
    }

    /** science.html: hero, stats, section titles and the call to action. */
    private function page(): void
    {
        $page = Page::forKey('lab');
        if ($page->hero_title && ! $this->force) {
            return;
        }
        $html = $this->design->page('science.html');
        $page->fill([
            'meta_description' => ['ar' => (string) $html->filter('meta[name="description"]')->attr('content', '')],
            'hero_eyebrow' => ['ar' => $this->text($html, '.lab-kicker')],
            'hero_title' => ['ar' => $this->text($html, '#lab-title')],
            'hero_lead' => ['ar' => $this->text($html, '.lab-hero-summary')],
            'hero_cta_label' => ['ar' => $this->text($html, '.lab-cta .btn')],
            'hero_cta_url' => '/contact',
        ]);
        $stats = $html->filter('.lab-stat');
        $s = fn (int $i, string $sel) => $stats->count() > $i ? DesignSource::text($stats->eq($i)->filter($sel)) : '';
        $heads = $html->filter('.lx-head');
        $h = fn (int $i, string $sel) => $heads->count() > $i ? DesignSource::text($heads->eq($i)->filter($sel)) : '';
        $page->extra = array_map(fn ($v) => ['ar' => $v], array_filter([
            'subtitle' => $this->text($html, '.lab-hero-subtitle'),
            'stat_inventions' => (string) $stats->eq(0)->filter('b')->attr('data-count', ''), 'stat_inventions_label' => $s(0, 'small'),
            'stat_science' => (string) $stats->eq(1)->filter('b')->attr('data-count', ''), 'stat_science_label' => $s(1, 'small'),
            'stats_note' => $this->text($html, '.lab-src'),
            'showcase_tag' => $h(0, '.lx-tag'), 'showcase_title' => $h(0, 'h2'), 'table_tag' => $h(1, '.lx-tag'), 'table_title' => $h(1, 'h2'),
            'cta_title' => $this->text($html, '.lab-cta b'), 'cta_text' => $this->text($html, '.lab-cta small'),
        ])) + ($page->extra ?? []);
        $page->save();

        $this->attach($page, 'cutout', (string) $html->filter('.sci-cut')->attr('src', ''));
        if ($this->force) {
            $page->clearMediaCollection('hero');
        }
        if (! $page->hasMedia('hero')) {
            $html->filter('.lab-hero-portraits img')->each(fn (Crawler $img) => $this->attach($page, 'hero', $img->attr('src'), always: true));
        }
    }

    /** "مايو ٢٠٢٦" -> first day of that month. */
    private function date(string $label, int $year): string
    {
        $month = 1;
        foreach (self::MONTHS as $i => $name) {
            if (str_contains($label, $name)) {
                $month = $i + 1;
            }
        }

        return Carbon::create($year, $month, 1)->toDateString();
    }

    /** @return array{ar: string}|null */
    private function t(?string $value): ?array
    {
        return filled($value) ? ['ar' => $value] : null;
    }

    private function text(Crawler $root, string $selector): string
    {
        $node = $root->filter($selector);

        return $node->count() ? DesignSource::text($node->first()) : '';
    }

    private function attach(HasMedia $model, string $collection, ?string $src, ?string $alt = null, bool $always = false): ?Media
    {
        if (! $src || (! $always && $model->hasMedia($collection) && ! $this->force)) {
            return null;
        }
        $path = ltrim(strtok($src, '?'), '/');
        try {
            $file = $this->design->file($path);
        } catch (\RuntimeException) {
            $this->report->note("Image not found in the design: $path");

            return null;
        }
        if ($this->force && ! $always) {
            $model->clearMediaCollection($collection);
        }

        return $model->addMedia($file)->preservingOriginal()
            ->withCustomProperties(array_filter(['alt' => $alt], fn ($v) => $v !== null))
            ->toMediaCollection($collection);
    }
}
