<?php

namespace App\Support\DesignImport\Steps;

use App\Models\Achiever;
use App\Models\Award;
use App\Models\AwardEdition;
use App\Models\Page;
use App\Models\Redirect;
use App\Support\ArabicSearch;
use App\Support\DesignImport\DesignSource;
use App\Support\DesignImport\ImportReport;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Awards from the design's data files (award-data.js, award-volunteer-data.js + award-volunteer-media.js,
 * bilarab-data.js as JSON) and the texts written in award.html, award-volunteer.html, bha-final.js and awards.html.
 */
class AwardsStep implements ImportStep
{
    /** Bilarab: photos of each cycle (design js/bha-final.js GAL): src, caption, credit. */
    private const BILARAB_GALLERY = [
        2022 => [
            ['assets/img/bilarab/muttrah-arch.jpg', 'التصميم الفائز «ميدان مطرح»: القوس المطل على البحر', 'تايمز أوف عُمان'],
            ['assets/img/bilarab/muttrah-bridge.jpg', 'الجسر الأيقوني فوق مياه كورنيش مطرح', 'عُمان أوبزرفر'],
            ['assets/img/bilarab/muttrah-steps.jpg', 'المدرجات المطلّة على الواجهة البحرية', 'عُمان أوبزرفر'],
            ['assets/img/bilarab/muttrah-night.jpg', 'الميدان ليلًا', 'عُمان أوبزرفر'],
            ['assets/img/bilarab/muttrah-plan.jpg', 'المخطط العام للميدان', 'عُمان أوبزرفر'],
            ['assets/img/bilarab/ceremony-2022.jpg', 'من حفل تتويج الفائزين بالدورة الأولى – ميدان دار الأوبرا السلطانية مسقط، مارس 2022', 'OERLive'],
            ['assets/img/bilarab/designs/c1-2-ceremony.jpg', 'تتويج المركز الثاني: ناصر الهيملي عن «نصب التأسيس التذكاري»', 'الرؤية'],
            ['assets/img/bilarab/designs/c1-3-ceremony.jpg', 'تتويج المركز الثالث: طاهرة البوصافية عن «جناح مستقبل عُمان»', 'الرؤية'],
            ['assets/img/bilarab/muttrah-handover-2022.jpg', 'تسليم المشروع الفائز إلى بلدية مسقط لتنفيذه – سبتمبر 2022', 'عُمان أوبزرفر'],
        ],
        2023 => [
            ['assets/img/bilarab/sur-museum-night.jpg', 'التصميم الفائز «متحف عُمان البحري» ليلًا', 'حساب الجائزة – ويكيميديا'],
            ['assets/img/bilarab/sur-museum-aerial.jpg', 'منظر علوي للمتحف على شاطئ صور', 'حساب الجائزة – ويكيميديا'],
            ['assets/img/bilarab/ceremony-2023-a.jpg', 'صاحب السمو يكرّم الفائزين – فبراير 2023', 'عُمان أوبزرفر'],
            ['assets/img/bilarab/ceremony-2023-b.jpg', 'من حفل تتويج الدورة الثانية – الجامعة الألمانية للتكنولوجيا، فبراير 2023', 'عُمان أوبزرفر'],
        ],
    ];

    /** Bilarab "who can enter": short text in the circles and icon (bha-final.js WHO). */
    private const BILARAB_WHO = [
        'الجنسية' => ['عُماني أو عُمانية', 'flag'], 'العمر' => ['حتى ٣٥ عامًا', 'age'], 'التخصص' => ['العمارة والتصميم والتخطيط', 'ruler'],
        'المشاركة' => ['فرد أو فريق حتى ٣', 'team'], 'المهارة' => ['إتقان برامج التصميم', 'pc'],
    ];

    /** Bilarab: map pins of the cycles (bha-final.js bf-omap). */
    private const BILARAB_PINS = [1 => ['مطرح', 85.8, 39.9], 2 => ['صور', 98, 54.2]];

    private DesignSource $design;

    private ImportReport $report;

    private bool $force;

    /** @var array<string, int> normalized achiever name -> id */
    private array $achievers = [];

    public function key(): string
    {
        return 'awards';
    }

    public function description(): string
    {
        return 'Awards, editions and winners';
    }

    public function run(DesignSource $design, ImportReport $report, bool $force): void
    {
        [$this->design, $this->report, $this->force] = [$design, $report, $force];
        $this->achievers = Achiever::query()->get(['id', 'name'])
            ->mapWithKeys(fn (Achiever $a) => [ArabicSearch::personKey($a->getTranslation('name', 'ar')) => $a->id])->all();

        $done = array_filter([$this->culture(), $this->volunteer(), $this->bilarab()]);
        $this->listPage();
        $this->redirect('award.html', '/awards/culture');
        $this->redirect('award-volunteer.html', '/awards/volunteer');
        $this->redirect('bilarab-award.html', '/awards/bilarab');

        $winners = Award::query()->withCount('winners')->get()->sum('winners_count');
        $report->done('awards: '.count($done).' imported ('.implode(', ', $done).'); '.$winners.' winners in total');
    }

    private function culture(): ?string
    {
        $d = $this->design->data('award-culture.json');
        $html = $this->design->page('award.html');
        $award = $this->award('culture', 'culture', [
            'name' => ['ar' => $d['name']],
            'title' => ['ar' => $this->text($html, '.aw-hero h1')],
            'eyebrow' => ['ar' => $this->text($html, '.aw-hero .aw-eyebrow')],
            'decree' => $d['decree'],
            'founded_year' => (int) substr((string) $d['founded'], -4),
            'launched' => ['ar' => $d['founded']],
            'website' => $d['site'] ?? null,
            'about' => ['ar' => $d['about']],
            'lead' => ['ar' => $this->text($html, '#aw-how .aw-lead')],
            'extra' => [
                'pillars_title' => ['ar' => $this->text($html, '.aw-sub')],
                'winners_eyebrow' => ['ar' => $this->text($html, '.aw-win-head .aw-eyebrow')],
                'winners_note' => ['ar' => $this->text($html, '.aw-win-head p')],
                'word_title' => ['ar' => $this->text($html, '#aw-word-h')],
            ],
            'sort_order' => 1,
        ] + $this->card('award.html'));
        if (! $award) {
            return null;
        }
        $this->attach($award, 'logo', $d['logo'] ?? null);
        $this->goals($award, $d['goals']);

        $branches = [];
        foreach (array_values(array_keys($d['branches'])) as $i => $key) {
            $branches[$key] = $award->categories()->create(['kind' => 'branch', 'key' => $key, 'name' => ['ar' => $d['branches'][$key]['name']],
                'hint' => ['ar' => $d['branches'][$key]['hint']], 'sort_order' => $i + 1]);
        }
        foreach (array_values(array_keys($d['prizes'])) as $i => $key) {
            $p = $d['prizes'][$key];
            $award->categories()->create(['kind' => 'prize', 'key' => $key, 'name' => ['ar' => $p['label']], 'medal' => ['ar' => $p['medal']],
                'basis' => ['ar' => $p['basis']], 'sort_order' => $i + 1]);
        }
        $this->letter($award, $d['word'] ?? null);

        // Oldest first in the dashboard order; the page sorts as the design (newest first).
        $editions = array_reverse($d['editions']);
        $sort = 0;
        foreach ($editions as $e) {
            $edition = $award->editions()->create(['number' => $e['n'], 'year' => (string) $e['year'], 'type' => $e['type'],
                'note' => isset($e['note']) ? ['ar' => $e['note']] : null, 'withheld' => $e['withheld'] ?? null, 'sort_order' => ++$sort]);
            if ((int) $e['year'] === 2019) {
                // The design shows a pause (2020–2021) after 2019 in the alternation bar.
                $award->editions()->create(['year' => '20–21', 'type' => 'pause', 'sort_order' => ++$sort]);
            }
            foreach ($e['winners'] as $j => $w) {
                $winner = $edition->winners()->create([
                    'award_category_id' => $branches[$w['branch']]->id ?? null, 'achiever_id' => $this->achiever($w['name']),
                    'name' => ['ar' => $w['name']], 'work' => isset($w['work']) ? ['ar' => $w['work']] : null, 'field' => ['ar' => $w['field']],
                    'why' => isset($w['why']) ? ['ar' => $w['why']] : null, 'gender' => $w['g'] ?? null, 'is_group' => (bool) ($w['group'] ?? false),
                    'sort_order' => $j + 1,
                ]);
                $this->attach($winner, 'photo', 'assets/img/award/winners/'.$w['photo'].'.jpg');
            }
        }
        $c = $d['current'];
        $award->editions()->create(['number' => $c['n'], 'year' => (string) $c['year'], 'type' => $c['type'], 'status' => ['ar' => $c['status']],
            'open_fields' => array_map(fn ($f) => ['category' => $f[0], 'text' => ['ar' => $f[1]]], $c['fields']), 'is_current' => true, 'sort_order' => ++$sort]);
        $this->sources($award, $d['sources']);

        return 'culture';
    }

    private function volunteer(): ?string
    {
        $d = $this->design->data('award-volunteer.json');
        $html = $this->design->page('award-volunteer.html');
        $h1 = $html->filter('#v-h1');
        $date = $html->filter('.v-date');
        $award = $this->award('volunteer', 'volunteer', [
            'name' => ['ar' => $d['name']],
            'title' => ['ar' => trim(DesignSource::text($h1->filter('small')) ? str_replace(DesignSource::text($h1->filter('small')), '', DesignSource::text($h1)) : DesignSource::text($h1))],
            'subtitle' => ['ar' => DesignSource::text($h1->filter('small'))],
            'motto' => ['ar' => $d['slogan']],
            'date_line' => ['ar' => DesignSource::text($date->filter('b')).'|'.trim(str_replace(DesignSource::text($date->filter('b')), '', DesignSource::text($date)))],
            'founded_year' => 2011,
            'about' => ['ar' => $d['about']],
            'vision' => ['ar' => $d['vision']],
            'honor' => ['ar' => $d['honor']],
            'extra' => [
                'how_title' => ['ar' => $this->text($html, '#v-how-h')],
                'fields_title' => ['ar' => $this->text($html, '#v-how .v-sub')],
                'winners_eyebrow' => ['ar' => $this->text($html, '.v-win-head .v-eyebrow')],
                'winners_title' => ['ar' => $this->text($html, '#v-win-h')],
                'winners_note' => ['ar' => $this->text($html, '.v-win-head p')],
            ],
            'sort_order' => 2,
        ] + $this->card('award-volunteer.html'));
        if (! $award) {
            return null;
        }
        $this->attach($award, 'logo', $d['logo'] ?? null);
        $this->goals($award, $d['goals']);
        foreach ($d['criteria'] as $i => $c) {
            $award->facts()->create(['group' => 'criteria', 'label' => ['ar' => $c[0]], 'value' => ['ar' => $c[1]], 'sort_order' => $i + 1]);
        }
        $cats = [];
        foreach (array_values(array_keys($d['cats'])) as $i => $key) {
            $cats[$key] = $award->categories()->create(['kind' => 'category', 'key' => $key, 'name' => ['ar' => $d['cats'][$key]['name']],
                'hint' => ['ar' => $d['cats'][$key]['hint']], 'sort_order' => $i + 1]);
        }
        foreach ($d['fields'] as $i => $field) {
            $award->categories()->create(['kind' => 'field', 'key' => 'f'.($i + 1), 'name' => ['ar' => $field], 'sort_order' => $i + 1]);
        }

        foreach (array_reverse($d['editions']) as $i => $e) {
            $edition = $award->editions()->create(['number' => $e['n'], 'year' => (string) $e['year'], 'entries_count' => (int) $e['entries'],
                'note' => isset($e['note']) ? ['ar' => $e['note']] : null, 'is_partial' => (bool) ($e['partial'] ?? false), 'sort_order' => $i + 1]);
            if (isset($e['cover'])) {
                $this->attach($edition, 'cover', $e['cover'][0], null, $e['cover'][1], properties: ['caption' => ['ar' => $e['cover'][1]], 'credit' => $e['cover'][2] ?? null]);
            }
            foreach ($e['winners'] as $j => $w) {
                $winner = $edition->winners()->create([
                    'award_category_id' => $cats[$w['cat']]->id ?? null, 'achiever_id' => isset($w['name']) ? $this->achiever($w['name']) : null,
                    'rank' => (int) $w['rank'], 'name' => isset($w['name']) ? ['ar' => $w['name']] : null,
                    'work' => isset($w['project']) ? ['ar' => $w['project']] : null, 'why' => isset($w['why']) ? ['ar' => $w['why']] : null,
                    'place' => isset($w['place']) ? ['ar' => $w['place']] : null, 'gender' => $w['g'] ?? null, 'is_withheld' => (bool) ($w['withheld'] ?? false),
                    'source_title' => $w['src'][0] ?? null, 'source_url' => $w['src'][1] ?? null, 'sort_order' => $j + 1,
                ]);
                $this->attach($winner, 'photo', $w['photo'] ?? null, $w['pos'] ?? null, $w['name'] ?? null, properties: array_filter(['credit' => $w['credit'] ?? null]));
                $this->attach($winner, 'logo', $w['logo'] ?? null);
                foreach ($w['gallery'] ?? [] as $g) {
                    $this->attach($winner, 'gallery', $g[0], null, $g[1], always: true, properties: ['caption' => ['ar' => $g[1]]]);
                }
                foreach ($w['facts'] ?? [] as $k => $f) {
                    $winner->facts()->create(['group' => 'facts', 'label' => ['ar' => $f[0]], 'value' => ['ar' => $f[1]], 'sort_order' => $k + 1]);
                }
            }
        }
        $this->sources($award, $d['sources']);

        return 'volunteer';
    }

    private function bilarab(): ?string
    {
        $d = $this->design->data('award-bilarab.json');
        $award = $this->award('bilarab', 'architecture', [
            'name' => ['ar' => $d['name']],
            'title' => ['ar' => 'جائزة بلعرب بن هيثم'],
            'subtitle' => ['ar' => 'للتصميم المعماري'],
            'eyebrow' => ['ar' => 'مسابقة سنوية للشباب العُماني'],
            'motto' => ['ar' => $d['motto']],
            'launched' => ['ar' => $d['launched']],
            'founded_year' => (int) substr((string) $d['launched'], -4),
            'about' => ['ar' => $d['about']],
            'lead' => ['ar' => 'مسابقة محلية سنوية **برؤية عالمية**، يتنافس فيها الشباب العُماني على تصاميم معمارية تجمع الأصالة والمعاصرة **وتُنفَّذ على أرض الواقع**.'],
            'firsts' => ['ar' => $d['firsts']],
            'extra' => [
                'blessing' => ['ar' => 'بمباركة سامية'], 'blessing_by' => ['ar' => 'من جلالة السلطان هيثم بن طارق المعظم'],
                'vision' => ['ar' => 'رؤية عُمان 2040'], 'vision_text' => ['ar' => 'تطوير المحافظات معماريًا، كل دورة في محافظة'],
                'where' => ['ar' => 'موقع جديد كل دورة'], 'where_text' => ['ar' => 'موضوع وموقع حقيقي في عُمان'],
                'path_title' => ['ar' => 'من الفكرة إلى أرض الواقع'], 'firsts_label' => ['ar' => 'سابقة عربية'],
                'more_title' => ['ar' => 'ما بعد الدورتين'], 'crumb' => ['ar' => 'جائزة بلعرب'],
            ],
            'sort_order' => 3,
        ]);
        if (! $award) {
            return null;
        }
        $this->attach($award, 'mark', 'assets/img/bilarab/mark.png');
        $this->attach($award, 'hero', 'assets/img/bilarab/expo-visitors-wide.webp');
        $this->attach($award, 'vision_logo', 'assets/img/bilarab/vision-2040-gold.png', alt: 'شعار رؤية عُمان 2040');
        $this->attach($award, 'emblem', 'assets/img/oman-emblem-gold.png');
        foreach ($d['who'] as $i => $w) {
            [$short, $icon] = self::BILARAB_WHO[$w[0]] ?? [null, null];
            $award->facts()->create(['group' => 'who', 'label' => ['ar' => $w[0]], 'value' => ['ar' => $w[1]], 'short' => $short ? ['ar' => $short] : null,
                'icon' => $icon, 'sort_order' => $i + 1]);
        }
        foreach ($d['path'] as $i => $p) {
            $award->facts()->create(['group' => 'path', 'label' => ['ar' => $p[0]], 'value' => ['ar' => $p[1]], 'sort_order' => $i + 1]);
        }
        $this->letter($award, $d['word'] ?? null, 'إن الهدف الأسمى هو الاستماع إلى صوت الشباب بطريقة مختلفة');

        foreach ($d['cycles'] as $i => $c) {
            [$pin, $x, $y] = self::BILARAB_PINS[$c['n']] ?? [null, null, null];
            $edition = $award->editions()->create([
                'number' => $c['n'], 'year' => (string) $c['year'], 'title' => ['ar' => $c['title']], 'place' => ['ar' => $c['place']], 'area' => $c['area'] ?? null,
                'entries_count' => (int) $c['entries'], 'finals_count' => (int) $c['finals'], 'brief' => ['ar' => $c['brief']], 'jury' => ['ar' => $c['jury']],
                'jury_all' => isset($c['juryAll']) ? ['ar' => $c['juryAll']] : null, 'exec' => ['ar' => $c['exec']],
                'ceremony' => isset($c['ceremony']) ? ['ar' => $c['ceremony']] : null, 'map_label' => $pin ? ['ar' => $pin] : null, 'map_x' => $x, 'map_y' => $y,
                'sort_order' => $i + 1,
            ]);
            foreach (self::BILARAB_GALLERY[(int) $c['year']] ?? [] as $g) {
                $this->attach($edition, 'gallery', $g[0], null, $g[1], always: true, properties: ['caption' => ['ar' => $g[1]], 'credit' => $g[2]]);
            }
            foreach ($c['winners'] as $j => $w) {
                $winner = $edition->winners()->create([
                    'rank' => (int) $w['rank'], 'award_label' => isset($w['award']) ? ['ar' => $w['award']] : null,
                    'team' => array_map(fn ($n) => ['ar' => $n], $w['team']), 'work' => ['ar' => $w['project']], 'why' => isset($w['why']) ? ['ar' => $w['why']] : null,
                    'source_title' => $w['credit'][0] ?? null, 'source_url' => $w['credit'][1] ?? null, 'sort_order' => $j + 1,
                ]);
                foreach ($w['img'] ?? [] as $img) {
                    $this->attach($winner, 'designs', $img[0], null, $img[1], always: true, properties: ['caption' => ['ar' => $img[1]]]);
                }
            }
        }

        $expo = $d['expo'];
        $feature = $award->features()->create(['key' => 'expo', 'chip' => ['ar' => 'مسابقة خاصة'], 'title' => ['ar' => $expo['title']], 'text' => ['ar' => $expo['text']],
            'note' => isset($expo['note']) ? ['ar' => $expo['note']] : null, 'team' => array_map(fn ($n) => ['ar' => $n], $expo['team']),
            'entries_count' => preg_match('/(\d+) مشاركة/u', $expo['text'], $m) ? (int) $m[1] : null, 'sort_order' => 1]);
        foreach ($expo['img'] ?? [] as $img) {
            $this->attach($feature, 'images', $img[0], null, $img[1], always: true, properties: ['caption' => ['ar' => $img[1]], 'credit' => $img[2][0] ?? null, 'credit_url' => $img[2][1] ?? null]);
        }
        $ihya = $d['ihya'];
        $award->features()->create(['key' => 'ihya', 'chip' => ['ar' => $ihya['when']], 'title' => ['ar' => $ihya['title']], 'subtitle' => ['ar' => $ihya['where']],
            'text' => ['ar' => $ihya['text']], 'note' => filled($ihya['next'] ?? null) ? ['ar' => $ihya['next']] : null, 'sort_order' => 2]);
        $this->sources($award, $d['sources']);

        return 'bilarab';
    }

    /** awards.html hero and its portrait. */
    private function listPage(): void
    {
        $page = Page::forKey('awards');
        if ($page->hero_lead && ! $this->force) {
            return;
        }
        $html = $this->design->page('awards.html');
        $h1 = $html->filter('#awards-title');
        $page->setTranslation('hero_title', 'ar', trim(str_replace(DesignSource::text($h1->filter('.ap-shine')), '', DesignSource::text($h1))).'|'.DesignSource::text($h1->filter('.ap-shine')));
        $page->setTranslation('hero_lead', 'ar', $this->text($html, '.aws-hero-lead'));
        $page->setTranslation('hero_eyebrow', 'ar', $this->text($html, '.aws-hero-tribute'));
        $page->save();
        $this->attach($page, 'hero', 'assets/img/award/sultan-qaboos-cutout.png', alt: $html->filter('.aws-hero-portrait img')->attr('alt', ''));

        $fields = $html->filter('.aws-hero-fields li')->each(fn (Crawler $li) => DesignSource::text($li));
        foreach (['culture' => 0, 'volunteer' => 1] as $slug => $i) {
            $award = Award::query()->where('slug', $slug)->first();
            if ($award && isset($fields[$i])) {
                $award->forceFill(['extra' => ($award->extra ?? []) + ['field_label' => ['ar' => $fields[$i]]]])->save();
            }
        }
        $vol = Award::query()->where('slug', 'volunteer')->first();
        if ($vol) {
            $this->attach($vol, 'card_art', 'assets/img/volunteer/original-trophy-cutout.png');
        }
    }

    /** @return array<string, mixed> awards.html card texts for a page */
    private function card(string $href): array
    {
        $card = $this->design->page('awards.html')->filter('a.aws-card[href="'.$href.'"]');
        if (! $card->count()) {
            return [];
        }

        return [
            'card_meta' => ['ar' => $this->text($card, '.aws-txt small')],
            'card_text' => ['ar' => $this->text($card, '.aws-txt em')],
            'card_style' => str_contains((string) $card->attr('class'), 'is-vol') ? 'vol' : 'culture',
        ];
    }

    /** @param  array<string, mixed>  $attributes */
    private function award(string $slug, string $kind, array $attributes): ?Award
    {
        $award = Award::withTrashed()->firstOrNew(['slug' => $slug]);
        if ($award->exists && ! $this->force) {
            return null;
        }
        $award->fill($attributes + ['kind' => $kind, 'is_published' => true])->save();
        if ($award->trashed()) {
            $award->restore();
        }
        $award->editions()->get()->each(function (AwardEdition $edition) {
            $edition->winners()->get()->each->delete();
            $edition->delete();
        });
        $award->categories()->delete();
        $award->features()->get()->each->delete();
        $award->facts()->delete();
        $award->sources()->delete();

        return $award;
    }

    /** @param  list<string>  $goals */
    private function goals(Award $award, array $goals): void
    {
        foreach ($goals as $i => $goal) {
            $award->facts()->create(['group' => 'goals', 'value' => ['ar' => $goal], 'sort_order' => $i + 1]);
        }
    }

    /** @param  array<string, mixed>|null  $word */
    private function letter(Award $award, ?array $word, ?string $quote = null): void
    {
        if (! $word) {
            return;
        }
        $letter = $award->letter()->updateOrCreate([], [
            'title' => isset($word['title']) ? ['ar' => $word['title']] : null,
            'name' => ['ar' => $word['name']],
            'role' => isset($word['role']) ? ['ar' => $word['role']] : null,
            'body' => ['ar' => implode("\n\n", $word['paras'] ?? [])],
            'quote' => $quote ? ['ar' => $quote] : null,
            'source_text' => $word['source'][0] ?? null,
            'source_url' => $word['source'][1] ?? null,
            'is_published' => true,
        ]);
        $this->attach($letter, 'photo', $word['photo'] ?? null);
    }

    /** @param  list<array{0: string, 1?: string}>  $items */
    private function sources(Award $award, array $items): void
    {
        foreach ($items as $i => $s) {
            $award->sources()->create(['title' => ['ar' => $s[0]], 'url' => $s[1] ?? null, 'sort_order' => $i + 1]);
        }
    }

    private function achiever(string $name): ?int
    {
        return $this->achievers[ArabicSearch::personKey($name)] ?? null;
    }

    private function text(Crawler $root, string $selector): string
    {
        $node = $root->filter($selector);

        return $node->count() ? DesignSource::text($node->first()) : '';
    }

    /** @param  array<string, mixed>  $properties */
    private function attach(HasMedia $model, string $collection, ?string $src, ?string $focal = null, ?string $alt = null, bool $always = false, array $properties = []): ?Media
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
            ->withCustomProperties(array_filter(['focal' => $focal, 'alt' => $alt] + $properties, fn ($v) => $v !== null))
            ->toMediaCollection($collection);
    }

    private function redirect(string $from, string $to): void
    {
        Redirect::query()->updateOrCreate(['from_path' => Redirect::normalize($from)], ['to_path' => $to, 'status_code' => 301]);
    }
}
