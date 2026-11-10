<?php

namespace App\Livewire\Site;

use App\Models\Achiever;
use App\Models\Field;
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

    public function updated(): void
    {
        $this->resetPage();
    }

    public function setField(string $slug): void
    {
        $this->field = $slug;
        $this->resetPage();
    }

    /** Design: gender has no "all"; clicking the selected one again clears it. */
    public function toggleGender(string $gender): void
    {
        $this->gender = $this->gender === $gender ? '' : $gender;
        $this->resetPage();
    }

    public function clearAll(): void
    {
        $this->reset('q', 'field', 'gender');
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

    private function query(): Builder
    {
        $query = Achiever::query()->listed()->with(['field', 'media'])
            ->withCount(['achievements' => fn ($q) => $q->published()]);

        if ($this->field !== '') {
            $query->whereHas('field', fn ($q) => $q->where('slug', $this->field));
        }
        if (in_array($this->gender, ['m', 'f'], true)) {
            $query->where('gender', $this->gender);
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
