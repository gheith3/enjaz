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
 * المؤسسات «ديوان المؤسسات»: search, type tabs with counts, the featured organization and the organizations
 * grouped by type as arch tiles. Universities show their first five plus a link to the universities page
 * (they have their own list).
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

    /** @return Collection<int, OrganizationType> types in order, each with its published organizations_count (the tabs) */
    #[Computed]
    public function types(): Collection
    {
        return OrganizationType::counted();
    }

    #[Computed]
    /** @return Collection<int, Organization> up to three organizations marked «مميّزة», side by side */
    public function featured(): Collection
    {
        return Organization::query()->published()->where('is_featured', true)->with(['media', 'type'])->orderBy('sort_order')->orderBy('id')->take(3)->get();
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
