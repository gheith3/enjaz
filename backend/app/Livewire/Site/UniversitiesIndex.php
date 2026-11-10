<?php

namespace App\Livewire\Site;

use App\Models\Organization;
use App\Models\Page;
use App\Support\ArabicSearch;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * الجامعات والكليات (design universities.html, list part): universities first (the first one with a profile as
 * the spotlight), then colleges, then the University of Technology with its branches. Group chips and search.
 */
class UniversitiesIndex extends Component
{
    public Page $sitePage;

    #[Url(except: '')]
    public string $q = '';

    #[Url(except: '')]
    public string $g = '';

    public function setGroup(string $group): void
    {
        $this->g = in_array($group, ['uni', 'college', 'tech'], true) ? $group : '';
    }

    /**
     * Linked counts per organization (design js/org-links.js summary()): own achievements, achievers linked
     * (people) and their achievements (members).
     *
     * @return array<int, array{own: int, people: int, members: int}>
     */
    #[Computed]
    public function counts(): array
    {
        $own = DB::table('achievement_organization')->join('achievements', 'achievements.id', '=', 'achievement_organization.achievement_id')
            ->where('achievements.is_published', true)->whereNull('achievements.deleted_at')
            ->groupBy('organization_id')->selectRaw('organization_id, count(*) as n')->pluck('n', 'organization_id');
        $people = DB::table('achiever_organization')->join('achievers', 'achievers.id', '=', 'achiever_organization.achiever_id')
            ->where('achievers.is_published', true)->whereNull('achievers.deleted_at')
            ->groupBy('organization_id')->selectRaw('organization_id, count(distinct achievers.id) as n')->pluck('n', 'organization_id');
        $members = DB::table('achiever_organization')
            ->join('achievement_achiever', 'achievement_achiever.achiever_id', '=', 'achiever_organization.achiever_id')
            ->join('achievements', 'achievements.id', '=', 'achievement_achiever.achievement_id')
            ->where('achievements.is_published', true)->whereNull('achievements.deleted_at')
            ->groupBy('organization_id')->selectRaw('organization_id, count(distinct achievements.id) as n')->pluck('n', 'organization_id');

        return collect($own->keys())->merge($people->keys())->unique()
            ->mapWithKeys(fn ($id) => [$id => ['own' => (int) ($own[$id] ?? 0), 'people' => (int) ($people[$id] ?? 0), 'members' => (int) ($members[$id] ?? 0)]])->all();
    }

    /** @return Collection<int, Organization> */
    private function organizations(): Collection
    {
        $query = Organization::query()->listed()->ofType('academic')->with(['media', 'stats', 'units' => fn ($q) => $q->where('kind', 'branch')]);
        if (trim($this->q) !== '') {
            ArabicSearch::where($query, ['name', 'short_name', 'city'], $this->q);
        }

        return $query->get();
    }

    public function render(): View
    {
        $all = $this->organizations();
        $unis = $all->where('academic_group', 'uni')->values();
        $spot = trim($this->q) === '' ? $unis->first(fn (Organization $o) => filled($o->about)) : null;

        return view('livewire.site.universities-index', [
            'groups' => collect(['uni' => $unis, 'college' => $all->where('academic_group', 'college')->values(), 'tech' => $all->where('academic_group', 'tech')->values()])
                ->filter(fn ($items, $group) => $this->g === '' || $this->g === $group),
            'spot' => $spot,
        ]);
    }
}
