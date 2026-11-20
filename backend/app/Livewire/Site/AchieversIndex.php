<?php

namespace App\Livewire\Site;

use App\Models\Achiever;
use App\Models\Field;
use App\Models\Organization;
use App\Models\Page;
use App\Support\ArabicCount;
use App\Support\ArabicSearch;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * المنجزين (design achievers.html): hero with faces and search, the week pair, field chips, gender filter
 * and the paginated grid (15 per page). Filters live in the query string.
 */
class AchieversIndex extends Component
{
    use WithPagination;

    public Page $sitePage;

    #[Url(except: '')]
    public string $q = '';

    #[Url(except: '')]
    public string $field = '';

    #[Url(except: '')]
    public string $gender = '';

    /** Selected sports federation (organization slug), as on the achievements page. */
    #[Url(except: '')]
    public string $org = '';

    /** Fields that open the federations panel. */
    private const FEDERATION_FIELDS = ['sports'];

    public function updated(): void
    {
        $this->resetPage();
    }

    public function setField(string $slug): void
    {
        // A second press on an open federations field (its ✕) returns to all fields.
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
     * Federations of the open field with the number of their achievers under the current filters.
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
                'count' => $this->query(withOrg: false)->whereHas('organizations', fn (Builder $q) => $q->where('slug', $o->slug))->count(),
                'url' => $o->url(),
            ])->values()->all();
    }

    /** @return array{slug: string, name: string, short: string, logo: ?string, ratio: float, count: int, url: string}|null */
    #[Computed]
    public function activeFederation(): ?array
    {
        return $this->org === '' ? null : (collect($this->federations)->firstWhere('slug', $this->org) ?: null);
    }

    /** Design: gender has no "all"; clicking the selected one again clears it. */
    public function toggleGender(string $gender): void
    {
        $this->gender = $this->gender === $gender ? '' : $gender;
        $this->resetPage();
    }

    public function clearAll(): void
    {
        $this->reset('q', 'field', 'gender', 'org');
        $this->resetPage();
    }

    /** @return Collection<int, Field> */
    #[Computed]
    public function fields(): Collection
    {
        return Field::query()->active()->get();
    }

    /** @return Collection<int, Achiever> first faces of the hero */
    #[Computed]
    public function faces(): Collection
    {
        return Achiever::query()->listed()->with('media')->get()->filter(fn (Achiever $a) => $a->hasMedia('portrait'))->take(5)->values();
    }

    /**
     * Hero wall constellations: up to 4 fields with the most achievers, their portraits.
     *
     * @return list<array{n: string, f: list<string>}>
     */
    #[Computed]
    public function wall(): array
    {
        return Achiever::query()->listed()->with(['media', 'field'])->get()
            ->filter(fn (Achiever $a) => $a->field && $a->hasMedia('portrait'))
            ->groupBy('field_id')->sortByDesc(fn ($group) => $group->count())->take(4)
            ->map(fn ($group) => ['n' => (string) $group->first()->field->name, 'f' => $group->take(5)->map(fn (Achiever $a) => $a->imageUrl('portrait', 'card'))->values()->all()])
            ->values()->all();
    }

    private function query(bool $withOrg = true): Builder
    {
        $query = Achiever::query()->listed()->with(['field', 'media'])
            ->withCount(['achievements' => fn ($q) => $q->published()]);

        if ($this->field !== '') {
            $query->whereHas('field', fn ($q) => $q->where('slug', $this->field));
        }
        if (in_array($this->gender, ['m', 'f'], true)) {
            $query->where('gender', $this->gender);
        }
        if ($withOrg && $this->org !== '' && $this->hasFederations()) {
            $query->whereHas('organizations', fn (Builder $q) => $q->where('slug', $this->org));
        }
        if (trim($this->q) !== '') {
            $query->where(fn (Builder $q) => ArabicSearch::where($q, ['name', 'headline'], $this->q)
                ->orWhereHas('field', fn (Builder $f) => ArabicSearch::where($f, ['name'], $this->q)));
        }

        return $query;
    }

    public function render(): View
    {
        $results = $this->query()->paginate(15);

        return view('livewire.site.achievers-index', [
            'results' => $results,
            'countLabel' => ArabicCount::of($results->total(), 'achievers'),
            'week' => Achiever::ofTheWeek(),
        ]);
    }
}
