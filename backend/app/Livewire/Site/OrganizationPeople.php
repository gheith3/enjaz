<?php

namespace App\Livewire\Site;

use App\Models\Achiever;
use App\Models\Organization;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * منجزو الجامعة (design uni-people.html): achiever cards linked to the organization, filtered by
 * staff / students and graduates and by gender.
 */
class OrganizationPeople extends Component
{
    public Organization $organization;

    #[Url(except: '')]
    public string $kind = '';

    #[Url(except: '')]
    public string $gender = '';

    public function setKind(string $kind): void
    {
        $this->kind = in_array($kind, ['staff', 'student'], true) ? $kind : '';
    }

    public function toggleGender(string $gender): void
    {
        $this->gender = $this->gender === $gender || ! in_array($gender, ['m', 'f'], true) ? '' : $gender;
    }

    /** @return Collection<int, Achiever> */
    #[Computed]
    public function people(): Collection
    {
        return $this->organization->achievers()->published()->with(['media', 'field'])->withCount(['achievements' => fn ($q) => $q->published()])->get()
            ->unique('id')->values();
    }

    public function render(): View
    {
        $items = $this->people->filter(fn (Achiever $p) => (! $this->kind || ($p->pivot->kind === 'staff' ? 'staff' : 'student') === $this->kind)
            && (! $this->gender || $p->gender === $this->gender))->values();

        return view('livewire.site.organization-people', ['items' => $items]);
    }
}
