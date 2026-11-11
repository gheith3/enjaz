<?php

namespace App\Support\DesignImport\Steps;

use App\Models\Achiever;
use App\Models\LabItem;
use App\Models\Page;
use App\Models\PageSection;
use App\Support\ArabicSearch;
use App\Support\DesignImport\DesignMap;
use App\Support\DesignImport\DesignSource;
use App\Support\DesignImport\ImportReport;
use Spatie\MediaLibrary\HasMedia;
use Symfony\Component\DomCrawler\Crawler;

/**
 * ملهمون من عُمان (inspiring.html): the inspiring people (achievers with field, tagline and three photos), the page texts
 * and «هِمَمٌ بلا حدود» items. Run before the home step (its inspiring section shows these people).
 */
class InspiringStep implements ImportStep
{
    /** Photo key in the design file names => slug and gender. */
    private const PEOPLE = [
        'mohammed' => ['mohammed-almashaikhi', 'm'], 'safiya' => ['safiya-albahlani', 'f'],
        'laith' => ['laith-alkindi', 'm'], 'zainab' => ['zainab-alharrasi', 'f'],
    ];

    private DesignSource $design;

    private ImportReport $report;

    private bool $force;

    public function key(): string
    {
        return 'inspiring';
    }

    public function description(): string
    {
        return 'Inspiring page: people and همم بلا حدود';
    }

    public function run(DesignSource $design, ImportReport $report, bool $force): void
    {
        [$this->design, $this->report, $this->force] = [$design, $report, $force];
        $html = $design->page('inspiring.html');
        Page::syncFromConfig();   // the page and its sections must exist (fresh database)
        $existing = Achiever::query()->get()->keyBy(fn (Achiever $a) => ArabicSearch::personKey($a->getTranslation('name', 'ar')));
        $focal = fn (Crawler $img) => preg_match('/object-position:\s*([^;"]+)/', (string) $img->attr('style'), $m) ? trim($m[1]) : null;

        $count = 0;
        $html->filter('.is-panel')->each(function (Crawler $panel, int $i) use ($existing, $focal, $html, &$count) {
            $img = $panel->filter('img');
            $key = collect(array_keys(self::PEOPLE))->first(fn ($k) => str_contains((string) $img->attr('src'), $k));
            $name = $this->text($panel, '.is-name');
            $a = $existing[ArabicSearch::personKey($name)] ?? Achiever::query()->firstOrNew(['slug' => self::PEOPLE[$key][0] ?? 'inspiring-'.($i + 1)]);
            if ($a->exists && $a->is_inspiring && ! $this->force) {
                return;
            }
            $a->fill([
                'name' => $a->exists ? $a->getTranslations('name') : ['ar' => $name], 'gender' => $a->gender ?? (self::PEOPLE[$key][1] ?? null),
                'is_inspiring' => true, 'inspiring_field' => ['ar' => $this->text($panel, '.is-field')], 'inspiring_tagline' => ['ar' => $this->text($panel, '.is-line')],
                'is_published' => true, 'sort_order' => $a->sort_order ?: $i + 1,
            ])->save();
            $count++;
            $this->attach($a, 'inspiring_panel', $img->attr('src'), $name, $focal($img));
            if ($key) {
                $tile = $html->filter('.wall .tile:not([data-sample]) img[src*="inspiring-'.$key.'"]');
                $this->attach($a, 'portrait', 'assets/img/inspiring-'.$key.'.jpg', $name, $tile->count() ? $focal($tile) : null);
                $this->attach($a, 'inspiring_cover', 'assets/img/inspiring-'.$key.'-cover-v1.jpg', $name);
            }
        });

        $this->page($html);
        $this->himam($html);
        $report->done("inspiring: $count people; ".Achiever::query()->where('is_inspiring', true)->count().' inspiring in total');
        $report->note('Inspiring: the short lines under the names are editorial phrases (not quotes), as the design notes.');
    }

    private function page(Crawler $html): void
    {
        $page = Page::forKey('inspiring');
        if ($page->hero_title && ! $this->force) {
            return;
        }
        $title = $html->filter('#is-title');
        $gold = $this->text($title, '.is-gold');
        $page->fill([
            'meta_description' => ['ar' => (string) $html->filter('meta[name="description"]')->attr('content', '')],
            'hero_eyebrow' => ['ar' => $this->text($html, '.is-eyebrow')],
            'hero_title' => ['ar' => $gold.'|'.trim(mb_substr(DesignSource::text($title), mb_strlen($gold)))],
            'hero_lead' => ['ar' => $this->text($html, '.is-intro')],
        ]);
        $nom = $html->filter('.nominate');
        $page->extra = array_map(fn ($v) => ['ar' => $v], array_filter([
            'crumb' => $this->text($html, '.site-header .crumb'), 'discover' => trim(str_replace('←', '', $this->text($html, '.is-discover'))), 'wall_title' => $this->text($html, '#wall-h'),
            'cta_title' => $this->text($nom, 'b'), 'cta_text' => $this->text($nom, 'small'), 'cta_button' => $this->text($nom, '.btn'),
        ])) + ($page->extra ?? []);
        $page->save();
    }

    private function himam(Crawler $html): void
    {
        $page = Page::forKey('inspiring');
        $section = PageSection::query()->where('page_id', $page->id)->where('key', 'inspiring.himam')->first();
        if (! $section || ($section->items()->exists() && ! $this->force)) {
            return;
        }
        $root = $html->filter('[data-hm]');
        $h2 = $root->filter('#hm-h');
        $em = $this->text($h2, 'em');
        $section->forceFill(['settings' => [
            'eyebrow' => ['ar' => $this->text($root, '.hm-kick')],
            'title' => ['ar' => trim(mb_substr(DesignSource::text($h2), 0, mb_strlen(DesignSource::text($h2)) - mb_strlen($em))).'|'.$em],
            'lead' => ['ar' => $this->text($root, '.hm-head p')],
        ]])->save();
        $section->items()->get()->each->delete();
        $root->filter('.hm-lead, .hm-item')->each(function (Crawler $a, int $i) use ($section) {
            $href = (string) $a->attr('href');
            $lab = preg_match('/id=([\w-]+)/', $href, $m) ? LabItem::query()->where('slug', $m[1])->first() : null;
            $path = $lab ? null : DesignMap::path($href);
            if ($path === null && ! $lab) {
                $path = str_starts_with($href, 'award-volunteer.html') ? '/awards/volunteer'.(strstr($href, '#') ?: '') : null;
            } elseif ($path && str_contains($href, '#')) {
                $path .= strstr($href, '#');
            }
            $img = $a->filter('img');
            $item = $section->items()->create([
                'itemable_type' => $lab ? LabItem::class : null, 'itemable_id' => $lab?->id,
                'title' => ['ar' => $this->text($a, 'b')], 'label' => ['ar' => $this->text($a, '.hm-tag')],
                'text' => ($t = $this->text($a, '.hm-txt')) ? ['ar' => $t] : null, 'url' => $path,
                'settings' => ['meta' => ['ar' => $this->text($a, 'time')]], 'sort_order' => $i + 1,
            ]);
            $this->attach($item, 'image', $img->attr('src'), $img->attr('alt'));
        });
    }

    private function text(Crawler $root, string $selector): string
    {
        $node = $root->filter($selector);

        return $node->count() ? DesignSource::text($node->first()) : '';
    }

    private function attach(HasMedia $model, string $collection, ?string $src, ?string $alt = null, ?string $focal = null): void
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
        $model->addMedia($file)->preservingOriginal()->withCustomProperties(array_filter(['alt' => $alt, 'focal' => $focal]))->toMediaCollection($collection);
    }
}
