<?php

namespace App\Livewire\Site;

use App\Models\Achievement;
use App\Models\Field;
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
        $this->field = $slug;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('gender', 'from', 'to', 'scope', 'sort');
        $this->resetPage();
    }

    public function clearAll(): void
    {
        $this->reset('q', 'field', 'gender', 'from', 'to', 'scope', 'sort');
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

    /** Images of the hero wall: covers of recent achievements. @return list<array{url: string, gold: bool}> */
    #[Computed]
    public function heroTiles(): array
    {
        return Achievement::query()->published()->with('media')->orderByDesc('achieved_at')->limit(30)->get()
            ->map(fn (Achievement $a) => $a->imageUrl('cover', 'card'))->filter()->unique()->take(14)->values()
            ->map(fn (string $url, int $i) => ['url' => $url, 'gold' => $i % 5 === 1])->all();
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

    private function query(): Builder
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
        // The honour-board achievement is not repeated in the grid under "all" without a search (design rule).
        if ($this->field === '' && trim($this->q) === '' && $this->honor) {
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
