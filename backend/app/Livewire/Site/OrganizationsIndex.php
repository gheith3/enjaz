<?php

namespace App\Livewire\Site;

use App\Models\Organization;
use App\Models\OrganizationType;
use App\Models\Page;
use App\Support\ArabicSearch;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * المؤسسات (design institutions.html): type chips, search, organizations grouped by type. Universities show
 * their first five plus a link to the universities page (they have their own list).
 */
class OrganizationsIndex extends Component
{
    public Page $sitePage;

    #[Url(except: '')]
    public string $q = '';

    #[Url(except: '')]
    public string $type = '';

    public function setType(string $slug): void
    {
        $this->type = $slug;
    }

    /** @return Collection<int, OrganizationType> */
    #[Computed]
    public function types(): Collection
    {
        return OrganizationType::query()->orderBy('sort_order')->get();
    }

    /**
     * Counters of the design's rings: published organizations per type, universities excluded (they have
     * their own page), and the total.
     *
     * @return array{total: int, items: list<array{type: OrganizationType, count: int}>}
     */
    #[Computed]
    public function rings(): array
    {
        $counts = Organization::query()->published()->selectRaw('organization_type_id, count(*) as n')->groupBy('organization_type_id')->pluck('n', 'organization_type_id');
        $items = $this->types->reject(fn (OrganizationType $t) => $t->template === 'university')
            ->map(fn (OrganizationType $t) => ['type' => $t, 'count' => (int) ($counts[$t->id] ?? 0)])->filter(fn ($i) => $i['count'] > 0)->values()->all();

        return ['total' => array_sum(array_column($items, 'count')), 'items' => $items];
    }

    #[Computed]
    public function featured(): ?Organization
    {
        return Organization::query()->published()->where('is_featured', true)->with(['media', 'type'])->orderBy('sort_order')->first();
    }

    /** @return Collection<int, OrganizationType> grouped organizations (search + type filter applied) */
    private function groups(): Collection
    {
        $query = Organization::query()->listed()->with(['media', 'type']);
        if (trim($this->q) !== '') {
            ArabicSearch::where($query, ['name', 'short_name'], $this->q);
        }
        $organizations = $query->get()->groupBy('organization_type_id');
        $searching = trim($this->q) !== '';

        return $this->types
            ->filter(fn (OrganizationType $t) => $this->type === '' || $t->slug === $this->type)
            ->map(function (OrganizationType $t) use ($organizations, $searching) {
                $all = $organizations->get($t->id, collect());
                $short = $t->template === 'university' && ! $searching;

                return (object) ['type' => $t, 'total' => $all->count(), 'items' => $short ? $all->take(5) : $all, 'more' => $short && $all->isNotEmpty()];
            })
            ->filter(fn ($g) => $g->items->isNotEmpty())->values();
    }

    public function render(): View
    {
        return view('livewire.site.organizations-index', ['groups' => $this->groups()]);
    }
}
