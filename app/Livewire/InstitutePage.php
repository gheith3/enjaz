<?php

namespace App\Livewire;

use App\Models\Achievement;
use App\Models\Organization;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class InstitutePage extends Component
{
    public Organization $institute;

    public function mount(Organization $institute): void
    {
        $this->institute = $institute;
    }

    public function getRandomInstitutes(): Collection
    {
        return Organization::query()
            ->inRandomOrder()
            ->limit(4)
            ->get();
    }

    public function getAchievements(): Collection
    {
        return Achievement::query()
            ->whereHas('organizations', function ($query) {
                $query->where('id', $this->institute->id);
            })
            ->get();
    }

    public function render(): View
    {
        return view('livewire.institute-page');
    }
}
