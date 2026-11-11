<?php

namespace App\Support\DesignImport\Steps;

use App\Models\Page;
use App\Models\PageSection;
use App\Support\DesignImport\DesignSource;
use App\Support\DesignImport\ImportReport;
use Spatie\MediaLibrary\HasMedia;
use Symfony\Component\DomCrawler\Crawler;

/**
 * من نحن (about.html): hero, mission / vision / values (page texts), and the lists of the page script (fields, roadmap,
 * founder, testimonials, team, our achievements, media kit) as items of the page's sections. Data: about-page.json
 * and about-roadmap.json (the design's inline data, extracted), texts from about.html.
 */
class AboutStep implements ImportStep
{
    private DesignSource $design;

    private ImportReport $report;

    private bool $force;

    public function key(): string
    {
        return 'about';
    }

    public function description(): string
    {
        return 'About page';
    }

    public function run(DesignSource $design, ImportReport $report, bool $force): void
    {
        [$this->design, $this->report, $this->force] = [$design, $report, $force];
        Page::syncFromConfig();
        $html = $design->page('about.html');
        $data = $design->data('about-page.json');
        $page = Page::forKey('about');
        $this->page($page, $html);
        $sec = PageSection::query()->where('page_id', $page->id)->get()->keyBy('key');
        $title = fn (string $sel) => $this->text($html, $sel);

        $this->items($sec['about.fields'] ?? null, $data['fields'], fn ($f) => ['title' => $f['t'], 'text' => $f['d']],
            fn ($f) => ['image' => 'assets/img/'.$f['img'], 'icon' => 'assets/svg/'.$f['ico']],
            ['eyebrow' => $title('#fields .eyebrow'), 'title' => $this->emTitle($html->filter('#fields-title')), 'lead' => $title('#fields .intro p'), 'nav' => $data['nav'][2][1] ?? '']);
        $this->items($sec['about.journey'] ?? null, $design->data('about-roadmap.json'),
            fn ($m) => ['title' => strip_tags($m['s']), 'text' => $m['t'], 'label' => $m['date'], 'meta' => $m['y'], 'highlight' => ! empty($m['logo'])],
            fn ($m) => ['image' => 'assets/img/'.$m['img']],
            ['title' => $title('#journey-title'), 'lead' => $title('.journey-note'), 'nav' => $data['nav'][3][1] ?? '']);
        $founder = $html->filter('[data-founder]');
        $this->items($sec['about.founder'] ?? null, [1],
            fn () => ['title' => $this->text($founder, '.founder-top b'), 'label' => $this->text($founder, '.founder-top small'),
                'text' => implode("\n\n", $data['founder']), 'meta' => $this->text($founder, 'h3')],
            fn () => ['image' => $founder->filter('.founder-top img')->attr('src'), 'image2' => $founder->filter('.fd-photo img')->attr('src')],
            ['nav' => $data['nav'][4][1] ?? '']);
        $this->items($sec['about.testimonials'] ?? null, $data['quotes'],
            fn ($q) => ['title' => $q['n'], 'label' => $q['r'], 'text' => $q['q'], 'highlight' => $q['t'] === 'official'],
            fn ($q) => ['image' => 'assets/img/'.$q['img']], ['title' => $title('#t-title'), 'nav' => $data['nav'][5][1] ?? '']);
        $this->items($sec['about.team'] ?? null, $data['team'], fn ($t) => ['title' => $t['n'], 'label' => $t['r']],
            fn ($t) => ['image' => 'assets/img/'.$t['img']], ['title' => $title('#team-title'), 'nav' => $data['nav'][6][1] ?? '']);
        $this->items($sec['about.achievements'] ?? null, $data['achievements'], fn ($a) => ['title' => $a['t'], 'label' => $a['c']],
            fn ($a) => ['image' => 'assets/img/'.$a['img']], ['title' => $title('#a-title'), 'nav' => $data['nav'][7][1] ?? '']);
        $this->items($sec['about.media'] ?? null, $data['media'], fn ($m) => ['title' => $m['t']],
            fn ($m) => ['image' => 'assets/img/'.$m['img'], 'icon' => 'assets/svg/'.$m['ico']], ['title' => $title('#m-title'), 'nav' => $data['nav'][8][1] ?? '']);

        $report->done('about: page texts and '.PageSection::query()->where('page_id', $page->id)->withCount('items')->get()->sum('items_count').' section items');
        $report->note('About: the media kit items have no links in the design (they open the image); add a link (PDF, YouTube …) to each in the dashboard.');
    }

    private function page(Page $page, Crawler $html): void
    {
        if ($page->hero_title && ! $this->force) {
            return;
        }
        $hero = $html->filter('#top');
        $facts = $hero->filter('.ab-facts li')->each(fn (Crawler $li) => [$this->text($li, 'b'), $this->text($li, 'span')]);
        $values = $html->filter('#vmv-values .v-list li')->each(fn (Crawler $li) => [
            trim(DesignSource::text($li->filter('.v-tx'))) === '' ? '' : trim(str_replace($this->text($li, '.v-tag'), '', DesignSource::text($li->filter('.v-tx')))),
            $this->text($li, '.v-tag'),
        ]);
        $page->fill([
            'meta_description' => ['ar' => (string) $html->filter('meta[name="description"]')->attr('content', '')],
            'hero_eyebrow' => ['ar' => $this->text($hero, '.ab-kick')],
            'hero_title' => ['ar' => $this->emTitle($hero->filter('h1'))],
            'hero_lead' => ['ar' => $this->text($hero, '.hero-lead')],
            'hero_cta_url' => '/contact',
        ]);
        $extra = ['crumb' => $this->text($html, '.header .crumb'), 'mission' => $this->text($html, '#vmv-mission .lead'), 'vision' => $this->text($html, '#vmv-vision .lead'),
            'journey_button' => trim(str_replace('←', '', $this->text($hero, '.ab-btn'))), 'contact_button' => $this->text($hero, '.ab-ghost')];
        foreach ($facts as $i => [$n, $t]) {
            $extra['fact'.($i + 1).'_n'] = $n;
            $extra['fact'.($i + 1).'_t'] = $t;
        }
        foreach ($values as $i => [$t, $tag]) {
            $extra['value'.($i + 1)] = $t;
            $extra['value'.($i + 1).'_tag'] = $tag;
        }
        $page->extra = array_map(fn ($v) => ['ar' => $v], array_filter($extra)) + ($page->extra ?? []);
        $page->save();
        $img = $hero->filter('.hero-image-blend img');
        $this->attach($page, 'cutout', $img->attr('src'), $img->attr('alt'));
    }

    /**
     * Replaces a section's items with the design's list and fills its empty texts.
     *
     * @param  list<mixed>  $list
     * @param  callable(mixed): array<string, mixed>  $fields  title, label, text, meta, highlight
     * @param  callable(mixed): array<string, ?string>  $images  collection => design path
     * @param  array<string, string>  $texts
     */
    private function items(?PageSection $section, array $list, callable $fields, callable $images, array $texts): void
    {
        if (! $section) {
            return;
        }
        $settings = $section->settings ?? [];
        foreach (array_filter($texts) as $k => $v) {
            if ($this->force || empty($settings[$k])) {
                $settings[$k] = ['ar' => $v];
            }
        }
        $section->forceFill(['settings' => $settings])->save();
        if ($section->items()->exists() && ! $this->force) {
            return;
        }
        $section->items()->get()->each->delete();
        foreach ($list as $i => $row) {
            $f = $fields($row);
            $item = $section->items()->create([
                'title' => ['ar' => $f['title']], 'label' => filled($f['label'] ?? null) ? ['ar' => $f['label']] : null,
                'text' => filled($f['text'] ?? null) ? ['ar' => $f['text']] : null,
                'settings' => array_filter(['meta' => filled($f['meta'] ?? null) ? ['ar' => $f['meta']] : null, 'highlight' => $f['highlight'] ?? null]),
                'sort_order' => $i + 1,
            ]);
            foreach ($images($row) as $collection => $src) {
                $this->attach($item, $collection, $src, $collection === 'icon' ? null : $f['title']);
            }
        }
    }

    /** Heading with <em> parts -> "a |em| b". */
    private function emTitle(Crawler $h): string
    {
        if (! $h->count()) {
            return '';
        }

        return trim(preg_replace('/\s+/u', ' ', strip_tags(str_replace(['<em>', '</em>'], ['|', '|'], $h->html()))));
    }

    private function text(Crawler $root, string $selector): string
    {
        $node = $root->filter($selector);

        return $node->count() ? DesignSource::text($node->first()) : '';
    }

    private function attach(HasMedia $model, string $collection, ?string $src, ?string $alt = null): void
    {
        if (! $src || ($model->hasMedia($collection) && ! $this->force)) {
            return;
        }
        $path = ltrim(preg_replace('#^\./#', '', strtok($src, '?')), '/');
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
