<?php

namespace App\Support\DesignImport\Steps;

use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\Field;
use App\Models\Redirect;
use App\Models\Scope;
use App\Models\SocialLink;
use App\Support\DesignImport\DesignSource;
use App\Support\DesignImport\ImportReport;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Fields, scopes, achievers and achievements from the design:
 *  - achievers.html cards (the real ones; `data-sample` duplicates are skipped), profile pages for the full profiles,
 *    the "منجز/منجزة الأسبوع" pair;
 *  - achievements from the profiles' grids and achievements.html cards, enriched by the detail pages
 *    (story, highlights, pull quote, sources, facts, cover).
 * Records that already exist are left alone unless --force.
 */
class AchievementsStep implements ImportStep
{
    /** Design category icon -> field slug (achievements.html chips). */
    private const FIELD_SLUGS = [
        'cat-sports' => 'sports', 'cat-culture' => 'culture', 'cat-science' => 'science', 'cat-military' => 'military',
        'cat-medical' => 'medical', 'cat-innovation' => 'innovation', 'cat-literature' => 'literature',
        'cat-students' => 'students', 'cat-arts' => 'arts', 'cat-business' => 'business',
    ];

    private const SCOPES = [
        ['local', 'محلي', 'إنجاز محلي', false],
        ['gulf', 'خليجي', 'إنجاز خليجي', true],
        ['arab', 'عربي', 'إنجاز عربي', true],
        ['regional', 'إقليمي', 'إنجاز إقليمي', true],
        ['international', 'دولي', 'إنجاز دولي', true],
    ];

    /** Achievers whose design card has no data-person: their slug. */
    private const NAME_SLUGS = [
        'محمد عامر المالكي' => 'mohammed-almalki',
        'حمد الوهيبي' => 'hamed-alwahaibi',
        'عائشة السيفي' => 'aisha-alsaifi',
        'سيف الرحبي' => 'saif-alrahbi',
    ];

    /** Full profile pages. */
    private const PROFILES = ['jokha-alharthi' => 'profile.html', 'ali-alhabsi' => 'profile-ali-alhabsi.html'];

    /** Images the design marks as temporary (another person's photo). */
    private const TEMPORARY_IMAGES = ['inspiring-mohammed.jpg'];

    private DesignSource $design;

    private ImportReport $report;

    private bool $force;

    /** @var array<string, Field> keyed by Arabic name */
    private array $fields = [];

    /** @var array<string, Scope> keyed by slug */
    private array $scopes = [];

    public function key(): string
    {
        return 'achievements';
    }

    public function description(): string
    {
        return 'Fields, scopes, achievers and achievements';
    }

    public function run(DesignSource $design, ImportReport $report, bool $force): void
    {
        [$this->design, $this->report, $this->force] = [$design, $report, $force];

        $this->importFields();
        $this->importScopes();
        $achievers = $this->importAchievers();
        $this->importProfiles();
        $this->importWeek();
        $count = $this->importAchievements();

        $report->done('fields: '.Field::count().', scopes: '.Scope::count().", achievers: $achievers, achievements: $count");
    }

    private function importFields(): void
    {
        $this->design->page('achievements.html')->filter('[data-chips] .chip[data-filter]')->each(function (Crawler $chip, int $i) {
            $name = $chip->attr('data-filter');
            $icon = basename((string) $chip->filter('img')->attr('src', ''), '.svg');
            if ($name === '*' || ! isset(self::FIELD_SLUGS[$icon])) {
                return;
            }
            Field::query()->firstOrCreate(['slug' => self::FIELD_SLUGS[$icon]], [
                'name' => ['ar' => $name], 'icon' => $icon, 'card_tone' => $icon === 'cat-sports' ? 'sports' : 'culture', 'sort_order' => $i,
            ]);
        });

        // Card accent colours of the design (css/desktop.css: .ach[data-cat="..."] { --c }).
        preg_match_all('/\[data-cat="([^"]+)"\][^{]*\{\s*--c:\s*(#[0-9A-Fa-f]{3,8})/u', $this->design->read('css/desktop.css'), $colors, PREG_SET_ORDER);
        foreach ($colors as [, $name, $color]) {
            Field::query()->where('name->ar', $name)->whereNull('color')->update(['color' => $color]);
        }

        $this->fields = Field::all()->keyBy(fn (Field $f) => $f->getTranslation('name', 'ar'))->all();
    }

    private function importScopes(): void
    {
        foreach (self::SCOPES as $i => [$slug, $name, $label, $international]) {
            Scope::query()->firstOrCreate(['slug' => $slug], [
                'name' => ['ar' => $name], 'achievement_label' => ['ar' => $label], 'is_international' => $international, 'sort_order' => $i + 1,
            ]);
        }
        $this->scopes = Scope::all()->keyBy('slug')->all();
    }

    private function importAchievers(): int
    {
        $count = 0;
        $this->design->page('achievers.html')->filter('article.person-card')->each(function (Crawler $card, int $i) use (&$count) {
            if ($card->attr('data-sample') !== null) {
                return;
            }
            $name = DesignSource::text($card->filter('h3'));
            $slug = $card->attr('data-person') ?: (self::NAME_SLUGS[$name] ?? null);
            if (! $slug) {
                $this->report->note("Achiever \"$name\" has no slug mapping; skipped.");

                return;
            }

            $achiever = Achiever::withTrashed()->firstOrNew(['slug' => $slug]);
            if ($achiever->exists && ! $this->force) {
                return;
            }

            $achiever->fill([
                'name' => ['ar' => $name],
                'gender' => $card->attr('data-gender') === 'أنثى' ? 'f' : 'm',
                'field_id' => $this->fields[$card->attr('data-cat')]->id ?? null,
                'headline' => ['ar' => DesignSource::text($card->filter('.ach-headline'))],
                'is_featured' => $card->attr('data-featured') !== null,
                'is_published' => true,
                'sort_order' => $i + 1,
            ])->save();
            $count++;

            $img = $card->filter('img.ach-img');
            if (in_array(basename((string) $img->attr('src', '')), self::TEMPORARY_IMAGES, true)) {
                $this->report->note("$name: the design uses a temporary photo of someone else; no portrait imported. Add the real one in the dashboard.");
            } else {
                $this->attach($achiever, 'portrait', $img->attr('src', ''), $this->focal($img), $img->attr('alt', ''));
            }
        });

        $this->report->note('Skipped the design\'s duplicate sample cards (data-sample) on achievers.html and achievements.html.');

        return $count;
    }

    private function importProfiles(): void
    {
        foreach (self::PROFILES as $slug => $file) {
            $achiever = Achiever::query()->where('slug', $slug)->first();
            if (! $achiever || (! $this->force && $achiever->journeyItems()->exists())) {
                continue;
            }
            $page = $this->design->page($file);

            $hero = $page->filter('.profile-hero');
            $about = preg_split('#<br\s*/?>#i', $hero->filter('.about')->html(''));
            $card = $this->json('inline/'.basename($file, '.html').'--member-card-data.json');
            $trace = $hero->filter('path.hm-line')->attr('d', '');
            $kicker = $page->filter('.gj-lead small');
            $title = $page->filter('.gj-lead strong');
            $pq = $page->filter('figure.pq')->first();

            $achiever->setTranslation('name', 'en', DesignSource::text($hero->filter('.en')))
                ->setTranslation('hero_lines', 'ar', implode("\n", array_map(fn ($l) => trim(strip_tags($l)), $about)))
                ->setTranslation('short_bio', 'ar', (string) ($card['bio'] ?? ''))
                ->setTranslation('story', 'ar', $this->paragraphs($page->filter('#story-text')))
                ->setTranslation('journey_kicker', 'ar', $kicker->count() ? DesignSource::text($kicker) : '')
                ->setTranslation('journey_title', 'ar', $title->count() ? trim(strip_tags(preg_replace('#</?em>#', '*', $title->html()))) : '');
            if ($pq->count()) {
                $achiever->setTranslation('personal_quote', 'ar', DesignSource::text($pq->filter('blockquote')))
                    ->setTranslation('personal_quote_context', 'ar', $pq->filter('figcaption span')->count() ? DesignSource::text($pq->filter('figcaption span')) : '')
                    ->setTranslation('personal_quote_source', 'ar', $pq->filter('figcaption small')->count() ? DesignSource::text($pq->filter('figcaption small')) : '');
            }
            $note = $page->filter('.said-note');
            $achiever->setTranslation('quotes_note', 'ar', $note->count() ? DesignSource::text($note) : '');
            $achiever->portrait_trace = $trace;
            $achiever->is_featured = $achiever->is_featured || $hero->filter('.featured-mark')->count() > 0;
            $achiever->save();

            $photo = $hero->filter('img.photo');
            $this->attach($achiever, 'cutout', $photo->attr('src', ''), null, $photo->attr('alt', ''));
            $this->attach($achiever, 'avatar', $photo->attr('data-avatar', ''));
            $journeyPhoto = $page->filter('img.gj-ph');
            $this->attach($achiever, 'journey', $journeyPhoto->attr('src', ''), null, $journeyPhoto->attr('alt', ''));

            $achiever->journeyItems()->delete();
            $page->filter('.gj-list li')->each(fn (Crawler $li, int $i) => $achiever->journeyItems()->create([
                'title' => ['ar' => DesignSource::text($li->filter('.gj-t'))],
                'year' => DesignSource::text($li->filter('.gj-y')),
                'icon' => $li->attr('data-icon') ?: 'star',
                'sort_order' => $i + 1,
            ]));

            $achiever->quotes()->delete();
            $page->filter('figure.said-card')->each(function (Crawler $fig, int $i) use ($achiever) {
                $quote = $achiever->quotes()->create([
                    'text' => ['ar' => DesignSource::text($fig->filter('blockquote'))],
                    'name' => ['ar' => DesignSource::text($fig->filter('figcaption b'))],
                    'role' => ['ar' => DesignSource::text($fig->filter('figcaption small'))],
                    'sort_order' => $i + 1,
                ]);
                $avatar = (string) $fig->filter('.said-av img')->attr('src', '');
                if ($avatar && ! str_contains($avatar, '/team/')) {   // assets/img/team/* are generic placeholders
                    $this->attach($quote, 'avatar', $avatar);
                }
            });

            $achiever->socialLinks()->delete();
            $page->filter('a.profile-social')->each(function (Crawler $a, int $i) use ($achiever) {
                $platform = Str::after(basename((string) $a->filter('img')->attr('src', ''), '.svg'), 'icon-');
                if (in_array($platform, SocialLink::PLATFORMS, true)) {
                    $achiever->socialLinks()->create([
                        'platform' => $platform, 'url' => $a->attr('href', ''),
                        'handle' => DesignSource::text($a->filter('.profile-social-handle')), 'sort_order' => $i + 1,
                    ]);
                }
            });

            $achiever->clearMediaCollection('gallery');
            $page->filter('.media3[data-gallery] a')->each(function (Crawler $a) use ($achiever) {
                $media = $this->attach($achiever, 'gallery', $a->attr('href', ''), $this->focal($a->filter('img')), $a->attr('data-caption', ''), always: true);
                $media?->setCustomProperty('caption', ['ar' => (string) $a->attr('data-caption', '')])->save();
            });

            $this->redirect('/'.$file, "/achievers/$slug");
        }
    }

    private function importWeek(): void
    {
        $this->design->page('achievers.html')->filter('a.week-card')->each(function (Crawler $card) {
            $name = DesignSource::text($card->filter('.week-name'));
            $achiever = Achiever::query()->get()->first(fn (Achiever $a) => $a->getTranslation('name', 'ar') === $name
                || str_contains($a->getTranslation('name', 'ar'), $name));
            if (! $achiever || ($achiever->week_from && ! $this->force)) {
                return;
            }
            $achiever->week_from = today();
            $achiever->setTranslation('week_teaser', 'ar', DesignSource::text($card->filter('q')));
            $achiever->save();
            $this->attach($achiever, 'cutout', $card->filter('img.week-cut')->attr('src', ''));
        });
    }

    private function importAchievements(): int
    {
        $items = [];

        // 1. Profile grids: tags (profile_tag), dates, cards.
        foreach (self::PROFILES as $achieverSlug => $file) {
            $this->design->page($file)->filter('article.ach[id^="ach-"]')->each(function (Crawler $card) use (&$items, $achieverSlug) {
                $slug = Str::after($card->attr('id', ''), 'ach-');
                $tag = DesignSource::text($card->filter('.tags .tag')->first());
                $items[$slug] = $this->cardData($card, $achieverSlug) + [
                    'date_text' => DesignSource::text($card->filter('.date')),
                    'profile_tag' => isset($this->fields[$tag]) ? null : $tag,
                    'detail' => $this->detailFile($card->filter('a.ach-link')->attr('href', ''), $slug),
                ];
            });
        }

        // 2. achievements.html: featured flag, field, achievements without a profile.
        $this->design->page('achievements.html')->filter('#main article.ach, main article.ach')->each(function (Crawler $card) use (&$items) {
            if ($card->attr('data-sample') !== null || ! $card->attr('data-person')) {
                return;
            }
            $href = (string) $card->filter('a.ach-link')->attr('href', '');
            $slug = str_contains($href, '#ach-') ? Str::after($href, '#ach-')
                : (preg_match('/^achievement-(.+)\.html$/', $href, $m) ? $m[1] : $card->attr('data-person').'-'.$card->attr('data-date'));
            $data = [
                'date_text' => DesignSource::text($card->filter('time')),
                'featured' => $card->attr('data-featured') !== null,
                'field' => $card->attr('data-cat'),
                'detail' => str_starts_with($href, 'achievement-') ? $href : null,
            ] + $this->cardData($card, $card->attr('data-person'));
            $items[$slug] = array_merge($data, array_filter($items[$slug] ?? [], fn ($v) => $v !== null), ['featured' => $data['featured'], 'field' => $data['field']]);
        });

        $count = 0;
        foreach ($items as $slug => $data) {
            $count += (int) $this->saveAchievement($slug, $data);
        }

        return $count;
    }

    /** @return array<string, mixed> */
    private function cardData(Crawler $card, string $achieverSlug): array
    {
        $img = $card->filter('img.ach-img');

        return [
            'achiever' => $achieverSlug,
            'title' => DesignSource::text($card->filter('h3')),
            'excerpt' => DesignSource::text($card->filter('.desc')),
            'scope' => $card->attr('data-scope'),
            'date' => $card->attr('data-date'),
            'field' => $card->attr('data-cat'),
            'image' => $img->attr('src', ''),
            'focal' => $this->focal($img),
            'featured' => false,
        ];
    }

    private function detailFile(?string $href, string $slug): ?string
    {
        if ($href === 'achievement.html') {
            $journey = $this->json('inline/achievement--journey-data.json');

            return ($journey['current'] ?? null) === $slug ? 'achievement.html' : null;
        }

        return $href && preg_match('/^achievement-.+\.html$/', $href) ? $href : null;
    }

    /** @param  array<string, mixed>  $data */
    private function saveAchievement(string $slug, array $data): bool
    {
        $achievement = Achievement::withTrashed()->firstOrNew(['slug' => $slug]);
        if ($achievement->exists && ! $this->force) {
            return false;
        }

        $achiever = Achiever::query()->where('slug', $data['achiever'])->first();
        $field = $this->fields[$data['field']] ?? null;
        if (! $field) {
            $this->report->note("Achievement $slug: field \"{$data['field']}\" unknown; imported without a field.");
        }

        $achievement->fill([
            'title' => ['ar' => $data['title']],
            'excerpt' => ['ar' => $data['excerpt']],
            'scope_id' => $this->scopes[$data['scope']]->id ?? null,
            'field_id' => $field?->id,
            'profile_tag' => $data['profile_tag'] ?? null ? ['ar' => $data['profile_tag']] : null,
            'achieved_at' => $data['date'],
            'date_precision' => $this->precision($data['date_text'] ?? ''),
            'is_featured' => (bool) ($data['featured'] ?? false),
            'is_published' => true,
        ]);

        $image = $data['image'];
        if ($data['detail'] ?? null) {
            $image = $this->applyDetail($achievement, $data['detail']) ?: $image;
            $this->redirect('/'.$data['detail'], "/achievements/$slug");
        }
        $achievement->save();

        if ($achiever) {
            $achievement->achievers()->syncWithoutDetaching([$achiever->id => ['is_primary' => true]]);
        }
        $this->attach($achievement, 'cover', $image, $data['focal'], $data['title']);

        return true;
    }

    /**
     * Story, highlights, pull quote, sources and facts of a detail page. Returns its cover image.
     */
    private function applyDetail(Achievement $achievement, string $file): ?string
    {
        $page = $this->design->page($file);
        $facts = $page->filter('.story-facts dd')->each(fn (Crawler $dd) => DesignSource::text($dd));
        $article = $page->filter('[data-article]');

        $paragraphs = [];
        $quoteAfter = null;
        $article->children()->each(function (Crawler $node) use (&$paragraphs, &$quoteAfter) {
            if ($node->nodeName() === 'figure' && str_contains((string) $node->attr('class'), 'pq')) {
                $quoteAfter = count($paragraphs);
            } elseif (in_array($node->nodeName(), ['p', 'h2', 'h3', 'ul', 'ol'], true)) {
                $paragraphs[] = '<'.$node->nodeName().'>'.trim($node->html()).'</'.$node->nodeName().'>';
            }
        });

        $achievement->setTranslation('title', 'ar', DesignSource::text($page->filter('h1')) ?: $achievement->title);
        $achievement->setTranslation('body', 'ar', implode("\n", $paragraphs));
        $achievement->setTranslation('discipline', 'ar', $facts[1] ?? '');
        $achievement->setTranslation('country', 'ar', $facts[2] ?? '');
        $achievement->highlights = $page->filter('aside.highlights li')->each(function (Crawler $li) {
            $lead = DesignSource::text($li->filter('b'));

            return ['lead' => ['ar' => $lead], 'text' => ['ar' => trim(Str::after(DesignSource::text($li), $lead))]];
        });

        $pq = $article->filter('figure.pq');
        if ($pq->count()) {
            $achievement->setTranslation('pull_quote', 'ar', DesignSource::text($pq->filter('blockquote')));
            $achievement->setTranslation('pull_quote_by', 'ar', DesignSource::text($pq->filter('figcaption b')));
            $achievement->setTranslation('pull_quote_role', 'ar', $pq->filter('figcaption span')->count() ? DesignSource::text($pq->filter('figcaption span')) : '');
            $achievement->setTranslation('pull_quote_note', 'ar', $pq->filter('figcaption small')->count() ? DesignSource::text($pq->filter('figcaption small')) : '');
            $achievement->pull_quote_after = max(1, (int) $quoteAfter);
        }
        $achievement->save();

        $achievement->sources()->delete();
        $page->filter('section.sources li a')->each(fn (Crawler $a, int $i) => $achievement->sources()->create([
            'title' => ['ar' => DesignSource::text($a->filter('b'))],
            'subtitle' => ['ar' => $a->filter('span')->count() ? DesignSource::text($a->filter('span')) : ''],
            'url' => $a->attr('href', ''),
            'sort_order' => $i + 1,
        ]));

        return $page->filter('img.story-photo')->attr('src', '');
    }

    private function precision(string $text): string
    {
        $parts = preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY);

        return match (count($parts)) {
            0, 3 => 'day',
            2 => 'month',
            default => 'year',
        };
    }

    private function paragraphs(Crawler $node): string
    {
        if (! $node->count()) {
            return '';
        }
        $ps = $node->filter('p')->each(fn (Crawler $p) => DesignSource::text($p));

        return implode("\n\n", $ps ?: [DesignSource::text($node)]);
    }

    private function focal(Crawler $img): ?string
    {
        preg_match('/object-position:\s*([^;"]+)/', (string) $img->attr('style', ''), $m);
        $focal = isset($m[1]) ? str_replace(['center', 'top', 'bottom', 'left', 'right'], ['50%', '0%', '100%', '0%', '100%'], trim($m[1])) : null;

        return $focal && preg_match('/^\d{1,3}%\s+\d{1,3}%$/', $focal) ? $focal : null;
    }

    /**
     * Add a design image to a collection (once). Returns the media item.
     */
    private function attach(HasMedia $model, string $collection, ?string $src, ?string $focal = null, ?string $alt = null, bool $always = false): ?Media
    {
        if (! $src || (! $always && $model->hasMedia($collection) && ! $this->force)) {
            return null;
        }
        $path = strtok($src, '?');
        try {
            $file = $this->design->file($path);
        } catch (\RuntimeException) {
            $this->report->note("Image not found in the design: $path");

            return null;
        }

        return $model->addMedia($file)->preservingOriginal()
            ->withCustomProperties(array_filter(['focal' => $focal, 'alt' => $alt]))
            ->toMediaCollection($collection);
    }

    /** @return array<mixed> */
    private function json(string $name): array
    {
        try {
            return $this->design->data($name);
        } catch (\Throwable) {
            return [];
        }
    }

    private function redirect(string $from, string $to): void
    {
        Redirect::query()->updateOrCreate(['from_path' => Redirect::normalize($from)], ['to_path' => $to, 'status_code' => 301]);
    }
}
