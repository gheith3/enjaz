<?php

namespace App\Livewire;

use App\Models\AchievementMainType;
use Illuminate\Support\Collection;
use Livewire\Component;

class AchieversSectionFilters extends Component
{

    public Collection $primaryFilters;
    public $selectedFilter = '0-0';

    public function mount() : void
    {
        $this->setFilters();
    }

    private function setFilters() : void
    {
        $this->primaryFilters = AchievementMainType::query()
            ->with(['subTypes'])->get();
    }

    public function setFilter(string $filterName) : void
    {
        $this->selectedFilter = $filterName;
        $_filters = str($filterName)->explode('-');
        $this->dispatch('filterSelected', [
            'main' => $_filters[0],
            'sub' => $_filters[1]
        ]);
    }

    public function getFilterId($main, $sub = '0') : string
    {
        return $main . '-' . $sub;
    }

    public function render()
    {
        return view('livewire.achievers-section-filters');
    }
}
