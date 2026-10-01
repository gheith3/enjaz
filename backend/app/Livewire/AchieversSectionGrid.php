<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;

class AchieversSectionGrid extends Component
{
    public Collection $achievers;
    public string $searchQuery = '';
    public int $mainFilter = 0;
    public int $subFilter = 0;

    protected $listeners = [
        'filterSelected' => 'updateFilter',
        'searchUpdated' => 'updateSearch',
    ];

    public function mount()
    {
        $this->setAchievers();
    }

    public function updateFilter($filter): void
    {
        $this->mainFilter = $filter['main'];
        $this->subFilter = $filter['sub'];

        $this->setAchievers();
    }

    public function updateSearch($filter): void
    {
        $this->searchQuery = $filter['search-query'];
        $this->setAchievers();
    }

    private function setAchievers(): void
    {
        $this->achievers = \App\Models\PublicFigure::query()
            ->when($this->searchQuery != '', function ($query) {
                $query
                    ->where('name_ar', 'like', '%' . $this->searchQuery . '%')
                    ->orWhere('name_en', 'like', '%' . $this->searchQuery . '%');
            })
            ->when($this->mainFilter != 0, function ($query) {
                $query->whereHas('achievements', function ($query) {
                    $query->where('achievement_main_type_id', $this->mainFilter);
                });
            })
            ->when($this->subFilter != 0, function ($query) {
                $query->whereHas('achievements', function ($query) {
                    $query->where('sub_achievement_type_id', $this->subFilter);
                });
            })
            ->get();
    }

    public function getFilteredAchieversProperty()
    {
        if ($this->selectedFilter === 'الكل') {
            return $this->achievers;
        }

        return array_filter($this->achievers, function ($achiever) {
            return $achiever['category'] === $this->selectedFilter;
        });
    }

    public function render()
    {
        return view('livewire.achievers-section-grid');
    }
}
