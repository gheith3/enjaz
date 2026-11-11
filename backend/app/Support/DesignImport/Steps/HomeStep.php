<?php

namespace App\Support\DesignImport\Steps;

use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\Article;
use App\Models\Award;
use App\Models\LeaderQuote;
use App\Models\News;
use App\Models\Organization;
use App\Models\OrganizationType;
use App\Models\Page;
use App\Models\PageSection;
use App\Support\ArabicSearch;
use App\Support\DesignImport\DesignSource;
use App\Support\DesignImport\ImportReport;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\DomCrawler\Crawler;

/**
 * The home page (index.html): leaders' words, flags on the records each section shows (strip, latest, articles, science,
 * partners), the texts of each section and the people picked for spotlight / awards banner / sports / culture.
 * Run after achievements, organizations, awards, lab, news, magazine and inspiring.
 */
class HomeStep implements ImportStep
{
    /** «أحدث الإنجازات»: place => [how to find the achievement, short title on the home card]. */
    private const LATEST = [
        ['feature', 'title:بوكر'], ['card', 'achiever:سمية السيابية'], ['card', 'achiever:علي الحبسي'],
        ['row', 'title:ماليزيا'], ['row', 'title:الأدب العربي'],
    ];

    private DesignSource $design;

    private ImportReport $report;

    private bool $force;

    /** @var array<string, int> */
    private array $people = [];

    public function key(): string
    {
        return 'home';
    }

    public function description(): string
    {
        return 'Home page: leaders, sections and their records';
    }

    public function run(DesignSource $design, ImportReport $report, bool $force): void
    {
        [$this->design, $this->report, $this->force] = [$design, $report, $force];
        $html = $design->page('index.html');
        $this->people = Achiever::query()->get(['id', 'name'])->mapWithKeys(fn (Achiever $a) => [ArabicSearch::personKey($a->getTranslation('name', 'ar')) => $a->id])->all();
        $page = Page::forKey('home');
        if (! $page->exists) {
            $page->save();
            Page::syncFromConfig();
        }
        $sections = PageSection::query()->where('page_id', $page->id)->get()->keyBy('key');
        $section = fn (string $key) => $sections[$key] ?? null;

        $this->leaders($html);
        $this->strip($html, $section('home.achievers'));
        $this->stats($html, $section('home.stats'));
        $this->latest($html, $section('home.latest'));
        $this->spotlight($html, $section('home.spotlight'));
        $this->awards($html, $section('home.awards'));
        $this->texts($section('home.inspiring'), ['title' => $this->text($html, '#inspiring-title'), 'lead' => $this->text($html, '.inspiring-heading p')]);
        $this->articles($html, $section('home.articles'));
        $this->people($html, '.home-sports', $section('home.sports'), '.sports-person:not(.sports-dup)', '.sports-cutout', '.sports-field', 'h3');
        $this->people($html, '.home-culture', $section('home.culture'), '.person', 'img', '.badge', '.name');
        $this->science($html, $section('home.science'));
        $this->partners($html, $section('home.partners'));

        $report->done('home: '.LeaderQuote::query()->count().' leader slides, '.Achiever::query()->where('show_in_home_strip', true)->count().' strip achievers, '
            .Achievement::query()->where('show_on_home', true)->count().' latest achievements, '.Organization::query()->where('is_partner', true)->count().' partners');
    }

    private function leaders(Crawler $html): void
    {
        if (LeaderQuote::query()->exists() && ! $this->force) {
            return;
        }
        LeaderQuote::query()->get()->each->delete();
        $html->filter('.hm-slide')->each(function (Crawler $slide, int $i) {
            preg_match('/hm-slide--(\w+)/', (string) $slide->attr('class'), $m);
            $quote = LeaderQuote::query()->create([
                'variant' => array_key_exists($m[1] ?? '', LeaderQuote::VARIANTS) ? $m[1] : 'sultan',
                'honorific' => ['ar' => $this->text($slide, '.hd-t')], 'name' => ['ar' => $this->text($slide, '.hd-nm')],
                'postnominal' => ['ar' => $this->text($slide, '.hd-ps')], 'lead' => ['ar' => $this->text($slide, '.hc-lead')],
                'body' => ['ar' => $this->text($slide, '.hc-body')], 'sort_order' => $i + 1,
            ]);
            $img = $slide->filter('img.portrait');
            $media = $this->attach($quote, 'portrait', $img->attr('src'), $img->attr('alt'));
            $media?->setCustomProperty('width', (int) $img->attr('width'))->setCustomProperty('height', (int) $img->attr('height'))->save();
        });
    }

    private function strip(Crawler $html, ?PageSection $section): void
    {
        $missing = [];
        $html->filter('[data-strip] .strip button')->each(function (Crawler $b, int $i) use (&$missing) {
            $id = $this->achiever((string) $b->attr('data-name'));
            if (! $id) {
                $missing[] = $b->attr('data-name');

                return;
            }
            $a = Achiever::query()->find($id);
            $a->forceFill(['show_in_home_strip' => true])->save();
            if (! $a->hasMedia('portrait') && ! $a->hasMedia('avatar')) {
                $img = $b->filter('img');
                $focal = preg_match('/object-position:\s*([^;"]+)/', (string) $img->attr('style'), $m) ? trim($m[1]) : null;
                $this->attach($a, 'avatar', $img->attr('src'), focal: $focal ? str_replace(['center', 'top'], ['50%', '0%'], $focal) : null);
            }
        });
        if ($missing) {
            $this->report->note('Home strip: these people are not achievers on the site yet, so they are not in the strip: '.implode('، ', $missing).'.');
        }
        $this->texts($section, ['more_label' => trim(str_replace('←', '', $this->text($html, '.strip-discover')))]);
    }

    private function stats(Crawler $html, ?PageSection $section): void
    {
        if (! $section || ($section->settings && ! $this->force)) {
            return;
        }
        $stats = $html->filter('[data-gold-stats] .hs-i')->each(fn (Crawler $s) => [
            'label' => ['ar' => $this->text($s, '.hs-l')], 'value' => (int) $s->filter('.hs-num')->attr('data-n'),
        ]);
        $section->forceFill(['settings' => ['stats' => $stats]])->save();
        $this->report->note('Home stats: the design\'s numbers (221، 2002، 35) are kept in the section; clear them in the dashboard to count from the site.');
    }

    private function latest(Crawler $html, ?PageSection $section): void
    {
        $titles = $html->filter('.home-latest strong')->each(fn (Crawler $s) => DesignSource::text($s));
        foreach (self::LATEST as $i => [$slot, $find]) {
            [$by, $value] = explode(':', $find, 2);
            $query = Achievement::query();
            $achievement = $by === 'title'
                ? $query->whereRaw("title->>'ar' like ?", ['%'.$value.'%'])->first()
                : $query->whereHas('achievers', fn ($q) => $q->whereKey($this->achiever($value) ?? 0))->orderByDesc('achieved_at')->first();
            if (! $achievement) {
                $this->report->note('Home «أحدث الإنجازات»: no achievement found for «'.($titles[$i] ?? $value).'»; choose one in the dashboard.');

                continue;
            }
            if ($achievement->show_on_home && ! $this->force) {
                continue;
            }
            $achievement->forceFill(['show_on_home' => true, 'home_slot' => $slot, 'home_title' => isset($titles[$i]) ? ['ar' => $titles[$i]] : null])->save();
        }
        $this->texts($section, ['title' => $this->text($html, '#home-latest-title'), 'more_label' => $this->text($html, '.home-latest .btn-more')]);
    }

    private function spotlight(Crawler $html, ?PageSection $section): void
    {
        $root = $html->filter('.aisha-feature');
        $this->texts($section, ['title' => $this->text($root, '.aisha-label')]);
        if (! $section || ($section->items()->exists() && ! $this->force)) {
            return;
        }
        $section->items()->get()->each->delete();
        $name = $this->text($root, '#aisha-title');
        $link = $root->filter('.aisha-bio a');
        $item = $section->items()->create([
            'itemable_type' => ($id = $this->achiever($name)) ? Achiever::class : null, 'itemable_id' => $id,
            'title' => ['ar' => $name], 'text' => ['ar' => $this->text($root, '.aisha-bio p')],
            'url' => $link->count() ? $link->attr('href') : null,
            'link_label' => $link->count() ? ['ar' => trim(str_replace('←', '', DesignSource::text($link)))] : null,
        ]);
        $img = $root->filter('img.aisha-portrait');
        $this->attach($item, 'image', $img->attr('src'), $img->attr('alt'));
    }

    private function awards(Crawler $html, ?PageSection $section): void
    {
        $root = $html->filter('.home-awards');
        $this->texts($section, ['title' => $this->text($root, '#awards-title'), 'lead' => $this->text($root, '.ha-lead')]);
        // The squares show "kicker + name": the award's subtitle is the second line.
        $root->filter('.ha-sq')->each(function (Crawler $sq) {
            $slug = trim(str_replace(['/awards/', 'award.html', 'award-volunteer.html'], ['', 'culture', 'volunteer'], (string) $sq->attr('href')), '/');
            $award = Award::query()->where('slug', $slug)->first();
            if ($award && ! $award->subtitle) {
                $award->forceFill(['subtitle' => ['ar' => $this->text($sq, '.ha-name')]])->save();
            }
        });
        if (! $section || ($section->items()->exists() && ! $this->force)) {
            return;
        }
        $section->items()->get()->each->delete();
        $bh = $root->filter('.ha-bh');
        if (! $bh->count()) {
            return;
        }
        $title = trim(implode('|', array_map(fn ($l) => trim(strip_tags($l)), preg_split('#<br\s*/?>#', $bh->filter('b')->html()))));
        $award = Award::query()->where('slug', 'bilarab')->first();
        $item = $section->items()->create([
            'itemable_type' => $award ? Award::class : null, 'itemable_id' => $award?->id,
            'title' => ['ar' => $title], 'label' => ['ar' => $this->text($bh, 'small')], 'text' => ['ar' => trim($this->text($bh, 'em'), '«» ')],
            'settings' => ['meta' => ['ar' => implode('|', $bh->filter('.ha-bh-meta span')->each(fn (Crawler $s) => DesignSource::text($s)))]],
            'link_label' => ['ar' => trim(str_replace('←', '', $this->text($bh, '.ha-bh-cta')))],
        ]);
        $this->attach($item, 'image', $bh->filter('.ha-bh-bg')->attr('src'));
    }

    private function articles(Crawler $html, ?PageSection $section): void
    {
        $this->texts($section, ['title' => $this->text($html, '#articles-title')]);
        $lead = Article::query()->where('slug', 'after-the-applause')->first();
        $slugs = $html->filter('.article-mini-link')->each(fn (Crawler $a) => str_replace('articles.html#a-', '', (string) $a->attr('href')));
        foreach (array_filter([$lead?->slug, ...$slugs]) as $slug) {
            Article::query()->where('slug', $slug)->update(['show_on_home' => true]);
        }
    }

    /** Sports and culture rows: one item per person with the design's image, label and link. */
    private function people(Crawler $html, string $root, ?PageSection $section, string $card, string $imgSel, string $labelSel, string $nameSel): void
    {
        $box = $html->filter($root);
        $this->texts($section, ['title' => $this->text($box, 'h2'), 'lead' => $this->text($box, '.culture-sub'),
            'more_label' => trim(preg_replace('/\s+من المنجزين الرياضيين/u', '', $this->text($box, '.sports-more .btn-more')))]);
        if (! $section || ($section->items()->exists() && ! $this->force)) {
            return;
        }
        $section->items()->get()->each->delete();
        $box->filter($card)->each(function (Crawler $a, int $i) use ($section, $imgSel, $labelSel, $nameSel) {
            $name = $this->text($a, $nameSel);
            $href = (string) $a->attr('href');
            $id = $this->achiever($name);
            $achievement = null;
            if (! $id && str_contains($href, 'achievement')) {
                $achievement = Achievement::query()->whereRaw("title->>'ar' like ?", ['%'.$name.'%'])->first();
            }
            $item = $section->items()->create([
                'itemable_type' => $id ? Achiever::class : ($achievement ? Achievement::class : null), 'itemable_id' => $id ?? $achievement?->id,
                'title' => ['ar' => $name], 'label' => ['ar' => $this->text($a, $labelSel)],
                'url' => preg_match('#^https?://#', $href) ? $href : null, 'sort_order' => $i + 1,
            ]);
            $img = $a->filter($imgSel)->first();
            $focal = preg_match('/object-position:\s*([^;"]+)/', (string) $img->attr('style'), $m) ? trim($m[1]) : null;
            $this->attach($item, 'image', $img->attr('src'), $name, $focal ? str_replace(['center', 'top'], ['50%', '0%'], $focal) : null);
        });
    }

    private function science(Crawler $html, ?PageSection $section): void
    {
        $root = $html->filter('.science-editorial');
        $this->texts($section, ['title' => $this->text($root, '#science-title'), 'lead' => $this->text($root, '.science-subtitle'),
            'more_label' => $this->text($root, '.science-bottom .btn-more')]);
        $root->filter('a[href*="news.html"]')->each(function (Crawler $a) {
            $slug = substr(strrchr((string) $a->attr('href'), '#') ?: '', 6);   // #news-water -> water
            if ($slug) {
                News::query()->where('slug', $slug)->update(['show_on_home' => true]);
            }
        });
    }

    private function partners(Crawler $html, ?PageSection $section): void
    {
        $root = $html->filter('.home-partners');
        $rows = [];
        $orgs = Organization::query()->with('type')->get();
        $byName = $orgs->mapWithKeys(fn (Organization $o) => [ArabicSearch::normalize($o->getTranslation('name', 'ar')) => $o]);
        $missing = [];
        $types = OrganizationType::query()->get()->keyBy('slug');
        $rowType = ['government' => 'gov', 'private' => 'private', 'military' => 'military', 'sports' => 'sports'];
        $root->filter('.partners-row')->each(function (Crawler $row) use (&$rows, $byName, &$missing, $types, $rowType) {
            preg_match('/partners-row--(\w+)/', (string) $row->attr('class'), $m);
            $type = $types[$rowType[$m[1] ?? ''] ?? ''] ?? null;
            if ($type) {
                $rows[$type->slug] = ['ar' => $this->text($row, 'h3')];   // in the design's order
            }
            $logos = [];
            $row->filter('.partners-group')->first()->filter('[title]')->each(function (Crawler $p) use (&$logos) {
                $logos[(string) $p->attr('title')] ??= $p->filter('img')->attr('src');
            });
            foreach ($logos as $t => $logo) {
                $org = $byName[ArabicSearch::normalize($t)] ?? null;
                if (! $org && $type) {
                    // Logo only (no page yet): a draft organization that the partners band can show.
                    $org = Organization::query()->create(['name' => ['ar' => $t], 'slug' => 'partner-'.substr(md5($t), 0, 8),
                        'organization_type_id' => $type->id, 'is_published' => false]);
                    $this->attach($org, 'logo', $logo, $t);
                    $missing[] = $t;
                }
                if (! $org) {
                    continue;
                }
                $org->forceFill(['is_partner' => true])->save();

            }
        });
        if ($missing) {
            $this->report->note('Home partners: created as draft organizations (logo only): '.implode('، ', array_unique($missing)).'. Complete and publish them to give them a page.');
        }
        $this->texts($section, ['eyebrow' => $this->text($root, '.partners-eyebrow'), 'title' => $this->text($root, '#partners-title'),
            'more_label' => trim(str_replace('←', '', $this->text($root, '.partners-all')))], ['rows' => $rows, 'row_order' => array_keys($rows)]);
    }

    /**
     * Fills the section's text settings ({ar}) that are empty.
     *
     * @param  array<string, string>  $texts
     * @param  array<string, mixed>  $extra
     */
    private function texts(?PageSection $section, array $texts, array $extra = []): void
    {
        if (! $section) {
            return;
        }
        $settings = $section->settings ?? [];
        foreach (array_filter($texts) as $key => $value) {
            if ($this->force || empty($settings[$key])) {
                $settings[$key] = ['ar' => $value];
            }
        }
        foreach ($extra as $key => $value) {
            if ($this->force || empty($settings[$key])) {
                $settings[$key] = $value;
            }
        }
        $section->forceFill(['settings' => $settings])->save();
    }

    private function achiever(string $name): ?int
    {
        return $this->people[ArabicSearch::personKey($name)] ?? null;
    }

    private function text(Crawler $root, string $selector): string
    {
        $node = $root->filter($selector);

        return $node->count() ? DesignSource::text($node->first()) : '';
    }

    private function attach(HasMedia $model, string $collection, ?string $src, ?string $alt = null, ?string $focal = null): ?Media
    {
        if (! $src || ($model->hasMedia($collection) && ! $this->force)) {
            return null;
        }
        $path = ltrim(strtok($src, '?'), '/');
        try {
            $file = $this->design->file($path);
        } catch (\RuntimeException) {
            $this->report->note("Image not found in the design: $path");

            return null;
        }
        $model->clearMediaCollection($collection);

        return $model->addMedia($file)->preservingOriginal()
            ->withCustomProperties(array_filter(['alt' => $alt, 'focal' => $focal]))->toMediaCollection($collection);
    }
}
