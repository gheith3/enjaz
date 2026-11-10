<?php

namespace App\Support\DesignImport\Steps;

use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\Field;
use App\Models\News;
use App\Models\Organization;
use App\Models\OrganizationType;
use App\Models\Page;
use App\Models\Redirect;
use App\Models\Scope;
use App\Support\DesignImport\DesignSource;
use App\Support\DesignImport\ImportReport;
use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Organizations from the design: types and the institutions list (institutions.html), universities
 * (js/universities-data.js + detail files), federations (js/federations-data.js), the ministry page
 * (institution.html) and its news (org-news.html, sample content -> drafts); links of achievements and
 * achievers to organizations (data-orgs / data-affil on the design's cards); redirects.
 * Existing organizations are left alone unless --force.
 */
class OrganizationsStep implements ImportStep
{
    /** Design data-cat -> type slug, template, css class. */
    private const TYPES = [
        'حكومي' => ['gov', 'institution', 't-gov'],
        'عسكري' => ['military', 'institution', 't-mil'],
        'خاص' => ['private', 'institution', 't-pvt'],
        'أكاديمي' => ['academic', 'university', 't-edu'],
        'رياضي' => ['sports', 'federation', 't-sport'],
    ];

    /** Institutions of institutions.html without a data file: their slug. */
    private const INSTITUTION_SLUGS = [
        'وزارة التراث والسياحة' => 'ministry-heritage-tourism',
        'وزارة الثقافة والرياضة والشباب' => 'mcsy',
        'وزارة الدفاع' => 'ministry-of-defence',
        'الجيش السلطاني العُماني' => 'royal-army-of-oman',
        'سلاح الجو السلطاني العُماني' => 'royal-air-force-of-oman',
        'البحرية السلطانية العُمانية' => 'royal-navy-of-oman',
        'الحرس السلطاني العُماني' => 'royal-guard-of-oman',
        'شرطة عُمان السلطانية' => 'royal-oman-police',
        'الوصال' => 'alwisal',
        'الطيران العماني' => 'oman-air',
    ];

    /** The design's institution.html is the ministry's page. */
    private const MINISTRY = 'mcsy';

    private const SECTORS = ['حكومية' => 'gov', 'خاصة' => 'private', 'أهلية' => 'ahli'];

    private DesignSource $design;

    private ImportReport $report;

    private bool $force;

    /** @var array<string, OrganizationType> */
    private array $types = [];

    public function key(): string
    {
        return 'organizations';
    }

    public function description(): string
    {
        return 'Organizations, their news and links to achievers and achievements';
    }

    public function run(DesignSource $design, ImportReport $report, bool $force): void
    {
        [$this->design, $this->report, $this->force] = [$design, $report, $force];

        $this->importTypes();
        $universities = $this->importUniversities();
        $federations = $this->importFederations();
        $institutions = $this->importInstitutions();
        $this->importMinistryPage();
        $this->importPages();
        $news = $this->importNews();
        $links = $this->importLinks();
        $this->importRedirects();

        $report->done("organizations: $universities universities, $federations federations, $institutions institutions; $news news (drafts); $links links");
    }

    private function importTypes(): void
    {
        $page = $this->design->page('institutions.html');
        $i = 0;
        foreach (self::TYPES as $name => [$slug, $template, $css]) {
            $group = $page->filter(".orgs-group[data-cat=\"$name\"] span");
            $label = $page->filter(".org-card[data-cat=\"$name\"] .org-type");
            OrganizationType::query()->firstOrCreate(['slug' => $slug], [
                'name' => ['ar' => $name],
                'label' => ['ar' => $label->count() ? DesignSource::text($label->first()) : $name],
                'group_label' => ['ar' => $group->count() ? DesignSource::text($group) : $name],
                'template' => $template, 'css_class' => $css, 'sort_order' => ++$i,
            ]);
        }
        foreach (self::TYPES as $name => [$slug]) {
            $chip = $page->filter(".orgs-chips .chip[data-filter=\"$name\"] span");
            OrganizationType::query()->where('slug', $slug)->whereNull('chip_label')
                ->update(['chip_label' => $chip->count() ? json_encode(['ar' => DesignSource::text($chip)], JSON_UNESCAPED_UNICODE) : null]);
        }
        $this->types = OrganizationType::all()->keyBy('slug')->all();
    }

    private function importUniversities(): int
    {
        $count = 0;
        foreach ($this->design->data('universities.json') as $i => $u) {
            $org = $this->organization($u['slug'], 'academic', [
                'name' => self::t($u['name'], $u['en'] ?? null),
                'short_name' => isset($u['short']) ? ['ar' => $u['short']] : null,
                'abbr' => $u['abbr'] ?? null,
                'academic_group' => $u['group'] ?? null,
                'sector' => self::SECTORS[$u['sector'] ?? ''] ?? null,
                'city' => isset($u['city']) ? ['ar' => $u['city']] : null,
                'founded' => $u['founded'] ?? null,
                'formerly' => isset($u['formerly']) ? ['ar' => $u['formerly']] : null,
                'tagline' => isset($u['tagline']) ? ['ar' => $u['tagline']] : null,
                'about' => isset($u['about']) ? ['ar' => $u['about']] : null,
                'website' => $u['site'] ?? null,
                'logo_ratio' => $u['r'] ?? null,
                'color_primary' => $u['color'][0] ?? null,
                'color_secondary' => $u['color'][1] ?? null,
                'rankings' => isset($u['ranks']) ? array_map(fn ($r) => ['year' => $r[0], 'rank' => (string) $r[1]], $u['ranks']['items'] ?? []) : null,
                'ranking_label' => isset($u['ranks']['label']) ? ['ar' => $u['ranks']['label']] : null,
                'ranking_highlights' => isset($u['ranks']) ? array_map(fn ($h) => ['subject' => ['ar' => $h[0]], 'text' => ['ar' => $h[1]]], $u['ranks']['highlights'] ?? []) : null,
                'ranking_note' => isset($u['ranks']['hlNote']) ? ['ar' => $u['ranks']['hlNote']] : null,
                'gallery_chapters' => isset($u['library']['chapters']) ? array_map(fn ($c) => ['key' => $c[0], 'label' => ['ar' => $c[1]]], $u['library']['chapters']) : null,
                'gallery_credit' => isset($u['library']['credit']) ? ['text' => $u['library']['credit'][0], 'url' => $u['library']['credit'][1] ?? null] : null,
                'sort_order' => $i + 1,
            ]);
            if (! $org) {
                continue;
            }
            $count++;

            $this->attach($org, 'logo', $u['logo'] ?? null);
            $this->attach($org, 'logo_wide', $u['logoWide'] ?? null);
            $this->attach($org, 'cover', $u['cover'] ?? null);

            foreach ($u['stats'] ?? [] as $j => [$value, $label]) {
                $org->stats()->create(['value' => ['ar' => (string) $value], 'label' => ['ar' => $label], 'sort_order' => $j + 1]);
            }
            $sort = 0;
            foreach ($u['colleges'] ?? [] as $group) {
                foreach ($group['names'] as $k => $name) {
                    $org->units()->create(['kind' => 'college', 'year' => $group['year'] ?? null, 'name' => ['ar' => $name],
                        'note' => $k === 0 && ! empty($group['note']) ? ['ar' => $group['note']] : null, 'sort_order' => ++$sort]);
                }
            }
            foreach ($u['units'] ?? [] as $group) {
                foreach ($group['names'] as $name) {
                    $org->units()->create(['kind' => 'unit', 'group_label' => ['ar' => $group['label']], 'name' => ['ar' => $name], 'sort_order' => ++$sort]);
                }
            }
            foreach (['centers' => 'center', 'branches' => 'branch'] as $key => $kind) {
                foreach ($u[$key] ?? [] as $name) {
                    $org->units()->create(['kind' => $kind, 'name' => ['ar' => is_array($name) ? ($name['name'] ?? reset($name)) : $name], 'sort_order' => ++$sort]);
                }
            }
            $sort = 0;
            foreach (['presidents' => 'president', 'leaders' => 'leader', 'board' => 'board'] as $key => $kind) {
                foreach ($u[$key] ?? [] as $p) {
                    $leader = $org->leaders()->create([
                        'kind' => $kind, 'name' => ['ar' => $p['name']], 'title' => isset($p['title']) ? ['ar' => $p['title']] : null,
                        'role' => isset($p['role']) ? ['ar' => $p['role']] : null, 'from_year' => $p['from'] ?? null, 'to_year' => $p['to'] ?? null,
                        'is_current' => (bool) ($p['current'] ?? false), 'note' => isset($p['note']) ? ['ar' => $p['note']] : null, 'sort_order' => ++$sort,
                    ]);
                    $this->attach($leader, 'photo', $p['photo'] ?? null);
                }
            }
            $this->letter($org, $u['word'] ?? null);
            $this->milestones($org, $u['milestones'] ?? []);
            $this->sources($org, $u['sources'] ?? []);

            foreach ($u['library']['photos'] ?? [] as $photo) {
                $this->attach($org, 'gallery', $photo['src'], null, $photo['title'] ?? null, always: true, properties: array_filter([
                    'caption' => isset($photo['title']) ? ['ar' => $photo['title']] : null, 'title' => $photo['title'] ?? null,
                    'chapter' => $photo['chapter'] ?? null, 'year' => $photo['year'] ?? null, 'note' => $photo['note'] ?? null,
                    'credit' => ((array) ($photo['credit'] ?? []))[0] ?? null, 'credit_url' => ((array) ($photo['credit'] ?? []))[1] ?? null, 'kind' => empty($photo['doc']) ? 'photo' : 'doc',
                ]));
            }
            foreach ($u['library']['videos'] ?? [] as $j => $v) {
                $org->videos()->create(['youtube_id' => $v['yt'], 'title' => ['ar' => $v['title']], 'year' => $v['year'] ?? null,
                    'duration' => $v['dur'] ?? null, 'note' => isset($v['note']) ? ['ar' => $v['note']] : null,
                    'is_official' => (bool) ($v['official'] ?? false), 'sort_order' => $j + 1]);
            }
        }

        $this->report->note('Universities: only some records in the design have full details (about, presidents, library); the others have name, logo, city and website. The utas/sohar/dhofar details are marked "نموذج للمعاينة" in the design.');

        return $count;
    }

    private function importFederations(): int
    {
        $count = 0;
        foreach ($this->design->data('federations.json') as $i => $f) {
            $org = $this->organization($f['slug'], 'sports', [
                'name' => self::t($f['name'], $f['en'] ?? null),
                'short_name' => ['ar' => $f['short']],
                'abbr' => $f['abbr'] ?? null,
                'sport' => isset($f['sport']) ? ['ar' => $f['sport']] : ['ar' => $f['short']],
                'founded' => $f['founded'] ?? null,
                'declared' => $f['declared'] ?? null,
                'hq' => isset($f['hq']) ? ['ar' => $f['hq']] : null,
                'about' => isset($f['about']) ? ['ar' => $f['about']] : null,
                'website' => $f['site'] ?? null,
                'memberships' => isset($f['memberships']) ? array_map(fn ($m) => ['name' => ['ar' => $m[0]], 'year' => $m[1] ?? null], $f['memberships']) : null,
                'gallery_intro' => isset($f['gallery']['intro']) ? ['ar' => $f['gallery']['intro']] : null,
                'is_partner' => true,
                'sort_order' => $i + 1,
            ]);
            if (! $org) {
                continue;
            }
            $count++;
            $this->attach($org, 'logo', $f['logo']);

            foreach ($f['presidents'] ?? [] as $j => $p) {
                $leader = $org->leaders()->create(['kind' => 'president', 'name' => ['ar' => $p['name']], 'from_year' => $p['from'] ?? null,
                    'to_year' => $p['to'] ?? null, 'is_current' => (bool) ($p['current'] ?? false), 'note' => isset($p['note']) ? ['ar' => $p['note']] : null, 'sort_order' => $j + 1]);
                $this->attach($leader, 'photo', $p['photo'] ?? null);
            }
            $this->milestones($org, $f['milestones'] ?? []);
            $this->sources($org, $f['sources'] ?? []);
            foreach ($f['gallery']['photos'] ?? [] as $photo) {
                $this->attach($org, 'gallery', $photo['src'], null, $photo['title'] ?? null, always: true, properties: array_filter([
                    'caption' => isset($photo['title']) ? ['ar' => $photo['title']] : null, 'title' => $photo['title'] ?? null,
                    'era' => $photo['era'] ?? null, 'kind' => $photo['kind'] ?? 'photo', 'year' => $photo['year'] ?? null, 'note' => $photo['note'] ?? null,
                    'credit' => ((array) ($photo['credit'] ?? []))[0] ?? null, 'credit_url' => ((array) ($photo['credit'] ?? []))[1] ?? null,
                ]));
            }
            foreach ($f['gallery']['videos'] ?? [] as $j => $v) {
                $org->videos()->create(['youtube_id' => $v['yt'], 'title' => ['ar' => $v['title']], 'year' => $v['year'] ?? null,
                    'era' => $v['era'] ?? null, 'note' => isset($v['note']) ? ['ar' => $v['note']] : null, 'sort_order' => $j + 1]);
            }
        }
        $this->report->note('Federations: the design marks the federation logos as trial versions ("تجريبية") to be replaced by the official ones; only oman-fa has full details.');

        return $count;
    }

    private function importInstitutions(): int
    {
        $count = 0;
        $seen = [];
        $this->design->page('institutions.html')->filter('a.org-card')->each(function (Crawler $card, int $i) use (&$count, &$seen) {
            $name = DesignSource::text($card->filter('.org-info b'));
            $slug = self::INSTITUTION_SLUGS[$name] ?? null;
            [$typeSlug] = self::TYPES[$card->attr('data-cat')] ?? [null];
            if (! $slug || isset($seen[$slug]) || ! $typeSlug) {
                return;   // universities and federations come from their data files; the design repeats the ministry as filler
            }
            $seen[$slug] = true;
            $org = $this->organization($slug, $typeSlug, ['name' => ['ar' => $name], 'sort_order' => $i + 1, 'is_partner' => true]);
            if (! $org) {
                return;
            }
            $count++;
            $logo = (string) $card->filter('.org-logo img')->attr('src', '');
            if (str_contains($logo, 'cat-military')) {
                $this->report->note("$name: the design shows a generic military icon; add the real logo in the dashboard.");
            } else {
                $this->attach($org, 'logo', $logo);
            }
        });

        return $count;
    }

    private function importMinistryPage(): void
    {
        $org = Organization::query()->where('slug', self::MINISTRY)->first();
        if (! $org || ($org->about && ! $this->force)) {
            return;
        }
        $page = $this->design->page('institution.html');
        $org->focus_areas = $page->filter('.oh-fields span')->each(fn (Crawler $s) => ['ar' => DesignSource::text($s)]);
        $org->setTranslation('about', 'ar', implode("\n\n", $page->filter('[data-org-about] p')->each(fn (Crawler $p) => DesignSource::text($p))));
        $org->save();
        $this->attach($org, 'logo', $page->filter('.oh-logo img')->attr('src', ''));

        $word = $page->filter('section[data-word]');
        if ($word->count()) {
            $letter = $org->letter()->updateOrCreate([], [
                'name' => ['ar' => DesignSource::text($word->filter('.aisha-copy h2'))],
                'role' => ['ar' => DesignSource::text($word->filter('.of-role'))],
                'body' => ['ar' => implode("\n\n", $word->filter('.wc-text p')->each(fn (Crawler $p) => DesignSource::text($p)))],
                'is_published' => false,
            ]);
            $this->attach($letter, 'photo', $word->filter('img.aisha-portrait')->attr('src', ''));
            $this->report->note('Ministry: the minister\'s word in the design is placeholder text ("نص تجريبي"); imported hidden. Replace it and switch it on in the dashboard.');
        }
    }

    /**
     * The organizations page (hero slides, His Majesty's letter), the universities page hero, and the featured
     * organization of institutions.html.
     */
    private function importPages(): void
    {
        $page = Page::query()->where('key', 'organizations')->first();
        $html = $this->design->page('institutions.html');
        if ($page && ($this->force || ! $page->hero_title)) {
            $page->setTranslation('hero_title', 'ar', DesignSource::text($html->filter('.fh-text h1')));
            $page->setTranslation('hero_lead', 'ar', DesignSource::text($html->filter('.fh-text p')));
            $page->save();
            $page->clearMediaCollection('hero');
            $html->filter('.fh-slide img')->each(fn (Crawler $img) => $this->attach($page, 'hero', $img->attr('src', ''), null, $img->attr('alt', '') ?: null, always: true));

            $dialog = $html->filter('dialog.ol-dialog');
            $paragraphs = $dialog->filter('.ol-txt > p')->reduce(fn (Crawler $p) => ! in_array($p->attr('class'), ['ol-meta', 'ol-sign'], true))
                ->each(fn (Crawler $p) => DesignSource::text($p));
            $sign = $dialog->filter('.ol-sign');
            $letter = $page->letter()->updateOrCreate([], [
                'title' => ['ar' => DesignSource::text($dialog->filter('h2'))],
                'meta' => ['ar' => DesignSource::text($dialog->filter('.ol-meta'))],
                'quote' => ['ar' => DesignSource::text($html->filter('.ol-cap span')->first())],
                'body' => ['ar' => implode("\n\n", $paragraphs)],
                'name' => ['ar' => trim(strip_tags(explode('<br', $sign->html())[0]))],
                'role' => ['ar' => DesignSource::text($sign->filter('span'))],
                'is_published' => true,
            ]);
            $letter->clearMediaCollection('photo');
            $this->attach($letter, 'photo', $dialog->filter('img')->attr('src', ''), null, $dialog->filter('img')->attr('alt', '') ?: null);
        }

        $featured = $html->filter('a.orgs-featured');
        $ministry = Organization::query()->where('slug', self::MINISTRY)->first();
        if ($featured->count() && $ministry && ($this->force || ! $ministry->is_featured)) {
            $ministry->is_featured = true;
            $ministry->setTranslation('tagline', 'ar', DesignSource::text($featured->filter('.of-txt > span')));
            $ministry->save();
            $this->attach($ministry, 'logo_white', $featured->filter('.of-logo img')->attr('src', ''));
        }

        $universities = Page::query()->where('key', 'universities')->first();
        $uh = $this->design->page('universities.html')->filter('.uh-text');
        if ($universities && $uh->count() && ($this->force || ! $universities->hero_title)) {
            $universities->setTranslation('hero_eyebrow', 'ar', DesignSource::text($uh->filter('.uh-eyebrow')));
            $universities->setTranslation('hero_title', 'ar', trim(strip_tags(preg_replace('#</?span>#', '*', $uh->filter('h1')->html()))));
            $universities->setTranslation('hero_lead', 'ar', DesignSource::text($uh->filter('p')->first()));
            $universities->save();
        }
    }

    private function importNews(): int
    {
        $org = Organization::query()->where('slug', self::MINISTRY)->first();
        $count = 0;
        foreach ($this->design->data('org-news.json') as $id => $n) {
            $slug = self::MINISTRY.'-'.$id;
            $news = News::withTrashed()->firstOrNew(['slug' => $slug]);
            if ($news->exists && ! $this->force) {
                continue;
            }
            $body = '<p>'.e($n['p1']).'</p><p>'.e($n['p2']).'</p>'.preg_replace('#<img[^>]*>#', '', $n['body'] ?? '');
            $news->fill([
                'organization_id' => $org?->id, 'kind' => ($n['c'] ?? '') === 'ach' ? 'achievement' : 'news',
                'tag' => ['ar' => $n['tag'] ?? ''], 'title' => ['ar' => $n['t']], 'dek' => ['ar' => $n['dek'] ?? ''],
                'summary' => ['ar' => $n['dek'] ?? ''], 'body' => ['ar' => $body],
                'facts' => array_map(fn ($f) => ['label' => ['ar' => $f[0]], 'value' => ['ar' => $f[1]]], $n['facts'] ?? []),
                'happened_at' => self::arabicDate($n['d'] ?? '', $n['m'] ?? ''),
                'is_published' => false,
            ])->save();
            $this->attach($news, 'cover', 'assets/img/'.$n['img']);
            $news->clearMediaCollection('gallery');
            foreach ($n['gallery'] ?? [] as $img) {
                $this->attach($news, 'gallery', 'assets/img/'.$img, always: true);
            }
            $this->redirect("/org-news.html?id=$id", "/news/$slug");
            $count++;
        }
        if ($count) {
            $this->report->note('Organization news: the design marks these as sample content ("تجريبية"); imported as drafts.');
        }

        return $count;
    }

    /**
     * Achievements and achievers linked to organizations on the design's cards.
     */
    private function importLinks(): int
    {
        $orgs = Organization::query()->pluck('id', 'slug');
        $links = 0;

        // Achievements: data-orgs on achievements.html cards; the organization's own achievement (no person).
        $this->design->page('achievements.html')->filter('main article.ach')->each(function (Crawler $card) use ($orgs, &$links) {
            if ($card->attr('data-sample') !== null || ! $card->attr('data-orgs')) {
                return;
            }
            $ids = collect(explode(' ', $card->attr('data-orgs')))->map(fn ($s) => $orgs[$s] ?? null)->filter()->values();
            $title = DesignSource::text($card->filter('h3'));
            $achievement = Achievement::query()->where('title->ar', $title)->first() ?? ($card->attr('data-person') ? null : $this->ownAchievement($card, $title));
            if ($achievement && $ids->isNotEmpty()) {
                $achievement->organizations()->syncWithoutDetaching($ids->all());
                $links += $ids->count();
            }
        });

        // Achievers: data-affil "org|kind|role; ..." and data-orgs (membership) on achievers.html cards.
        $this->design->page('achievers.html')->filter('article.person-card')->each(function (Crawler $card) use ($orgs, &$links) {
            if ($card->attr('data-sample') !== null) {
                return;
            }
            $name = DesignSource::text($card->filter('h3'));
            $achiever = Achiever::query()->where('name->ar', $name)->first();
            if (! $achiever) {
                return;
            }
            foreach (array_filter(explode(';', (string) $card->attr('data-affil'))) as $affil) {
                [$slug, $kind, $role] = array_pad(array_map('trim', explode('|', $affil)), 3, null);
                if (isset($orgs[$slug])) {
                    $achiever->organizations()->syncWithoutDetaching([$orgs[$slug] => ['kind' => $kind ?: 'member', 'role' => ['ar' => $role]]]);
                    $links++;
                }
            }
            foreach (array_filter(explode(' ', (string) $card->attr('data-orgs'))) as $slug) {
                if (isset($orgs[$slug]) && ! $achiever->organizations()->whereKey($orgs[$slug])->exists()) {
                    $achiever->organizations()->attach($orgs[$slug], ['kind' => 'member']);
                    $links++;
                }
            }
        });

        return $links;
    }

    private function ownAchievement(Crawler $card, string $title): Achievement
    {
        $img = $card->filter('img.ach-img');
        $achievement = Achievement::query()->create([
            'slug' => 'squ-malaysia-inventions-gold',
            'title' => ['ar' => $title],
            'excerpt' => ['ar' => DesignSource::text($card->filter('.desc'))],
            'field_id' => Field::query()->where('name->ar', $card->attr('data-cat'))->value('id'),
            'scope_id' => Scope::query()->where('slug', $card->attr('data-scope'))->value('id'),
            'achieved_at' => $card->attr('data-date'),
            'date_precision' => 'month',
            'is_published' => true,
        ]);
        $this->attach($achievement, 'cover', $img->attr('src', ''), '50% 50%', $title);

        return $achievement;
    }

    private function importRedirects(): void
    {
        foreach (Organization::query()->with('type')->get() as $org) {
            $file = match ($org->type?->template) {
                'university' => 'university.html', 'federation' => 'federation.html', default => null,
            };
            if ($file) {
                $this->redirect("/$file?org={$org->slug}", "/organizations/{$org->slug}");
            }
            if ($org->type?->template === 'university') {
                $this->redirect("/uni-people.html?org={$org->slug}", "/organizations/{$org->slug}/people");
                $this->redirect("/uni-achievements.html?org={$org->slug}", "/organizations/{$org->slug}/achievements");
            }
        }
        $this->redirect('/institution.html', '/organizations/'.self::MINISTRY);
        $this->redirect('/institution-achievements.html', '/organizations/'.self::MINISTRY.'/achievements');
        $this->redirect('/university.html', '/organizations/squ');
        $this->redirect('/federation.html', '/organizations/oman-fa');
    }

    /**
     * Create (or, with --force, refill) an organization; null when it exists and is kept.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function organization(string $slug, string $type, array $attributes): ?Organization
    {
        $org = Organization::withTrashed()->firstOrNew(['slug' => $slug]);
        if ($org->exists && ! $this->force) {
            return null;
        }
        $org->fill($attributes + ['organization_type_id' => $this->types[$type]->id, 'is_published' => true])
            ->forceFill(['organization_type_id' => $this->types[$type]->id])->save();

        if ($this->force) {
            $org->stats()->delete();
            $org->units()->delete();
            $org->leaders()->get()->each->delete();
            $org->videos()->delete();
            $org->milestones()->delete();
            $org->sources()->delete();
            $org->clearMediaCollection('gallery');
        }

        return $org;
    }

    /** @param  array<string, mixed>|null  $word */
    private function letter(Organization $org, ?array $word): void
    {
        if (! $word) {
            return;
        }
        $letter = $org->letter()->updateOrCreate([], [
            'title' => isset($word['title']) ? ['ar' => $word['title']] : null,
            'name' => ['ar' => $word['name'] ?? ''],
            'role' => isset($word['role']) ? ['ar' => $word['role']] : null,
            'body' => ['ar' => implode("\n\n", $word['paras'] ?? [])],
            'source_text' => $word['source'][0] ?? null,
            'source_url' => $word['source'][1] ?? null,
            'is_published' => true,
        ]);
        $this->attach($letter, 'photo', $word['photo'] ?? null);
    }

    /** @param  list<array<string, mixed>>  $items */
    private function milestones(Organization $org, array $items): void
    {
        foreach ($items as $i => $m) {
            $org->milestones()->create(['year' => (string) $m['year'], 'title' => ['ar' => $m['title']],
                'place' => isset($m['place']) ? ['ar' => $m['place']] : null, 'sort_order' => $i + 1]);
        }
    }

    /** @param  list<array{0: string, 1?: string}>  $items */
    private function sources(Organization $org, array $items): void
    {
        foreach ($items as $i => $s) {
            $org->sources()->create(['title' => ['ar' => $s[0]], 'url' => $s[1] ?? null, 'sort_order' => $i + 1]);
        }
    }

    /** @return array<string, string> */
    private static function t(string $ar, ?string $en): array
    {
        return array_filter(['ar' => $ar, 'en' => $en]);
    }

    /** "١٥" + "أكتوبر ٢٠٢٤" -> 2024-10-15 */
    private static function arabicDate(string $day, string $monthYear): ?string
    {
        $digits = fn (string $s) => preg_replace_callback('/[٠-٩]/u', fn ($m) => (string) mb_strpos('٠١٢٣٤٥٦٧٨٩', $m[0]), $s);
        $months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
        [$monthName, $year] = array_pad(preg_split('/\s+/u', trim($monthYear)), 2, null);
        $month = array_search($monthName, $months, true);
        if ($month === false || ! $year) {
            return null;
        }

        return Carbon::create((int) $digits($year), $month + 1, max(1, (int) $digits($day)))->toDateString();
    }

    /**
     * Add a design image to a collection (once, or always for galleries). Returns the media item.
     *
     * @param  array<string, mixed>  $properties
     */
    private function attach(HasMedia $model, string $collection, ?string $src, ?string $focal = null, ?string $alt = null, bool $always = false, array $properties = []): ?Media
    {
        if (! $src || (! $always && $model->hasMedia($collection) && ! $this->force)) {
            return null;
        }
        $path = ltrim(strtok(str_replace('assets/img/../', 'assets/', $src), '?'), '/');
        try {
            $file = $this->design->file($path);
        } catch (\RuntimeException) {
            $this->report->note("Image not found in the design: $path");

            return null;
        }

        return $model->addMedia($file)->preservingOriginal()
            ->withCustomProperties(array_filter(['focal' => $focal, 'alt' => $alt]) + $properties)
            ->toMediaCollection($collection);
    }

    private function redirect(string $from, string $to): void
    {
        Redirect::query()->updateOrCreate(['from_path' => Redirect::normalize($from)], ['to_path' => $to, 'status_code' => 301]);
    }
}
