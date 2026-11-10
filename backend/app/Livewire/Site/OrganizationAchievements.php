<?php

namespace App\Livewire\Site;

use App\Models\Achievement;
use App\Models\Organization;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * إنجازات الجامعة (design uni-achievements.html): every achievement of the organization or its people, typed
 * org / student / staff, filtered by type and the achiever's gender, 12 at a time.
 */
class OrganizationAchievements extends Component
{
    public const TYPES = ['', 'student', 'staff', 'org'];

    public Organization $organization;

    #[Url(except: '')]
    public string $type = '';

    #[Url(except: '')]
    public string $gender = '';

    public int $limit = 12;

    public function setType(string $type): void
    {
        $this->type = in_array($type, self::TYPES, true) ? $type : '';
        $this->limit = 12;
    }

    public function toggleGender(string $gender): void
    {
        $this->gender = $this->gender === $gender || ! in_array($gender, ['m', 'f'], true) ? '' : $gender;
        $this->limit = 12;
    }

    public function more(): void
    {
        $this->limit += 12;
    }

    /** @return Collection<int, array{achievement: Achievement, type: string, gender: ?string}> */
    #[Computed]
    public function rows(): Collection
    {
        return $this->organization->allAchievements()->map(fn ($row) => $row + [
            'gender' => $row['achievement']->primaryAchiever()?->gender,
        ])->map(fn ($row) => ['type' => Organization::displayType($row['type'])] + $row);
    }

    public function render(): View
    {
        $rows = $this->rows;
        $byGender = $rows->filter(fn ($r) => ! $this->gender || $r['gender'] === $this->gender);
        $items = $byGender->filter(fn ($r) => ! $this->type || $r['type'] === $this->type)->values();

        return view('livewire.site.organization-achievements', [
            'counts' => collect(self::TYPES)->mapWithKeys(fn ($t) => [$t => $t ? $byGender->where('type', $t)->count() : $byGender->count()]),
            'totals' => collect(['org', 'student', 'staff'])->mapWithKeys(fn ($t) => [$t => $rows->where('type', $t)->count()]),
            'items' => $items,
        ]);
    }
}
