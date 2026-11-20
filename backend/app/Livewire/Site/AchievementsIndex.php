<?php

namespace App\Livewire\Site;

use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\Field;
use App\Models\Organization;
use App\Models\Page;
use App\Models\Scope;
use App\Support\ArabicCount;
use App\Support\ArabicDate;
use App\Support\ArabicSearch;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * الإنجازات (design achievements.html): hero with search, honour board, field chips, the "فلترة" popover
 * (gender, years, scope, sort) and the paginated grid. Filters live in the query string.
 */
class AchievementsIndex extends Component
{
    use WithPagination;

    public Page $sitePage;

    #[Url(except: '')]
    public string $q = '';

    #[Url(except: '')]
    public string $field = '';

    #[Url(except: '')]
    public string $gender = '';

    #[Url(except: '')]
    public string $from = '';

    #[Url(except: '')]
    public string $to = '';

    #[Url(except: '')]
    public string $scope = '';

    #[Url(except: 'new')]
    public string $sort = 'new';

    /** Selected sports federation (organization slug), shown in the federations panel under a field that has them. */
    #[Url(except: '')]
    public string $org = '';

    /** Fields that open the federations panel (design js/federations.js: groups by field). */
    private const FEDERATION_FIELDS = ['sports'];

    /** 13 per page on phones, 16 on desktop (the browser switches it, like the design's data-page-size-desktop). */
    public int $perPage = 13;

    public function updated(string $property): void
    {
        if ($property !== 'perPage' && $this->from !== '' && $this->to !== '' && $this->from > $this->to) {
            [$this->from, $this->to] = [$this->to, $this->from];
        }
        $this->resetPage();
    }

    public function setField(string $slug): void
    {
        // A second press on an open federations field (its ✕) returns to all fields, as in the design.
        $this->field = $slug === $this->field && $this->hasFederations() ? '' : $slug;
        $this->org = '';
        $this->resetPage();
    }

    /** Pick a federation; pressing the selected one again clears it. */
    public function setOrg(string $slug): void
    {
        $this->org = $slug === $this->org ? '' : $slug;
        $this->resetPage();
    }

    public function hasFederations(): bool
    {
        return in_array($this->field, self::FEDERATION_FIELDS, true);
    }

    /**
     * Federations of the open field with the number of achievements under the current filters (design js/federations.js render()).
     *
     * @return list<array{slug: string, name: string, short: string, logo: ?string, ratio: float, count: int, url: string}>
     */
    #[Computed]
    public function federations(): array
    {
        if (! $this->hasFederations()) {
            return [];
        }

        return Organization::query()->listed()->whereHas('type', fn (Builder $q) => $q->where('template', 'federation'))->with('media')->get()
            ->map(fn (Organization $o) => [
                'slug' => $o->slug,
                'name' => $o->name,
                'short' => $o->displayName(),
                'logo' => $o->logoUrl(),
                'ratio' => (float) ($o->logo_ratio ?: 1),
                'count' => $this->query(withOrg: false, withHonorRule: false)->where(fn (Builder $q) => $this->whereOrg($q, $o->slug))->count(),
                'url' => $o->url(),
            ])->values()->all();
    }

    /** @return array{slug: string, name: string, short: string, logo: ?string, ratio: float, count: int, url: string}|null */
    #[Computed]
    public function activeFederation(): ?array
    {
        return $this->org === '' ? null : (collect($this->federations)->firstWhere('slug', $this->org) ?: null);
    }

    /** Achievements of the federation itself or of its people (members, staff…). */
    private function whereOrg(Builder $query, string $slug): Builder
    {
        return $query->whereHas('organizations', fn (Builder $o) => $o->where('slug', $slug))
            ->orWhereHas('achievers.organizations', fn (Builder $o) => $o->where('slug', $slug));
    }

    public function clearFilters(): void
    {
        $this->reset('gender', 'from', 'to', 'scope', 'sort');
        $this->resetPage();
    }

    public function clearAll(): void
    {
        $this->reset('q', 'field', 'gender', 'from', 'to', 'scope', 'sort', 'org');
        $this->resetPage();
    }

    public function removeFilter(string $key): void
    {
        match ($key) {
            'years' => $this->reset('from', 'to'),
            'sort' => $this->reset('sort'),
            default => $this->reset($key),
        };
        $this->resetPage();
    }

    #[Computed]
    public function honor(): ?Achievement
    {
        return Achievement::query()->published()->where('is_featured', true)
            ->with(['field', 'media', 'achievers' => fn ($q) => $q->with('media')])
            ->orderByDesc('achieved_at')->first();
    }

    /** @return Collection<int, Field> */
    #[Computed]
    public function fields(): Collection
    {
        return Field::query()->active()->get();
    }

    /** @return Collection<int, Scope> */
    #[Computed]
    public function scopes(): Collection
    {
        return Scope::query()->orderBy('sort_order')->get();
    }

    /** @return list<string> years with published achievements, newest first */
    #[Computed]
    public function years(): array
    {
        return Achievement::query()->published()->whereNotNull('achieved_at')
            ->selectRaw('distinct extract(year from achieved_at)::int as y')->orderByDesc('y')->pluck('y')
            ->map(fn ($y) => (string) $y)->all();
    }

    /** The achievers whose portraits make the hero wall, in the approved design's order («صفحة الإنجازات - معتمد»). */
    private const HERO_ACHIEVERS = ['jokha-alharthi', 'ali-alhabsi', 'musallam-al-araimi', 'mohammed-almashaikhi', 'safiya-albahlani'];

    /**
     * Images of the hero wall: the portraits of the approved design's achievers (with their focal points); while fewer
     * than three of them have a portrait, the covers of recent achievements.
     *
     * @return list<array{url: string, focal: string}>
     */
    #[Computed]
    public function heroTiles(): array
    {
        $portraits = Achiever::query()->published()->whereIn('slug', self::HERO_ACHIEVERS)->with('media')->get()
            ->sortBy(fn (Achiever $a) => array_search($a->slug, self::HERO_ACHIEVERS, true))
            ->map(fn (Achiever $a) => ($url = $a->imageUrl('portrait', 'card')) ? ['url' => $url, 'focal' => Achiever::focal($a->image('portrait'))] : null)
            ->filter()->values();
        if ($portraits->count() >= 3) {
            return $portraits->all();
        }

        return Achievement::query()->published()->with('media')->orderByDesc('achieved_at')->limit(30)->get()
            ->map(fn (Achievement $a) => $a->imageUrl('cover', 'card'))->filter()->unique()->take(14)->values()
            ->map(fn (string $url) => ['url' => $url, 'focal' => '50% 50%'])->all();
    }

    /**
     * Active filters as removable chips (design js/ach-filters.js paint()).
     *
     * @return list<array{key: string, label: string}>
     */
    public function activeFilters(): array
    {
        $years = match (true) {
            $this->from !== '' && $this->to !== '' => $this->from === $this->to ? ArabicDate::digits($this->from) : ArabicDate::digits($this->from).' – '.ArabicDate::digits($this->to),
            $this->from !== '' => __('site.filters.from_year', ['y' => ArabicDate::digits($this->from)]),
            $this->to !== '' => __('site.filters.until_year', ['y' => ArabicDate::digits($this->to)]),
            default => null,
        };

        return array_values(array_filter([
            $this->gender !== '' ? ['key' => 'gender', 'label' => __('site.filters.'.($this->gender === 'f' ? 'female' : 'male'))] : null,
            $years ? ['key' => 'years', 'label' => $years] : null,
            $this->scope !== '' ? ['key' => 'scope', 'label' => (string) $this->scopes->firstWhere('slug', $this->scope)?->name] : null,
            $this->sort === 'old' ? ['key' => 'sort', 'label' => __('site.filters.oldest_first')] : null,
        ]));
    }

    private function query(bool $withOrg = true, bool $withHonorRule = true): Builder
    {
        $query = Achievement::query()->published()
            ->with(['field', 'scope', 'media', 'achievers' => fn ($q) => $q->with('media')]);

        if ($this->field !== '') {
            $query->whereHas('field', fn ($q) => $q->where('slug', $this->field));
        }
        if (in_array($this->gender, ['m', 'f'], true)) {
            $query->whereHas('achievers', fn ($q) => $q->where('gender', $this->gender));
        }
        if (ctype_digit($this->from)) {
            $query->whereYear('achieved_at', '>=', (int) $this->from);
        }
        if (ctype_digit($this->to)) {
            $query->whereYear('achieved_at', '<=', (int) $this->to);
        }
        if ($this->scope !== '') {
            $query->whereHas('scope', fn ($q) => $q->where('slug', $this->scope));
        }
        if (trim($this->q) !== '') {
            $query->where(fn (Builder $q) => ArabicSearch::where($q, ['title', 'excerpt'], $this->q)
                ->orWhereHas('achievers', fn (Builder $a) => ArabicSearch::where($a, ['name', 'headline'], $this->q)));
        }
        if ($withOrg && $this->org !== '' && $this->hasFederations()) {
            $query->where(fn (Builder $q) => $this->whereOrg($q, $this->org));
        }
        // The honour-board achievement is not repeated in the grid under "all" without a search (design rule).
        if ($withHonorRule && $this->field === '' && trim($this->q) === '' && $this->honor) {
            $query->whereKeyNot($this->honor->getKey());
        }

        return $query->orderBy('achieved_at', $this->sort === 'old' ? 'asc' : 'desc')->orderBy('achievements.id');
    }

    public function render(): View
    {
        $results = $this->query()->paginate(min(max($this->perPage, 1), 32));

        return view('livewire.site.achievements-index', [
            'results' => $results,
            'countLabel' => ArabicCount::of($results->total(), 'achievements'),
            'activeFilters' => $this->activeFilters(),
        ]);
    }
}
