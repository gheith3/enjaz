<?php

namespace App\Livewire;

use App\Models\OrganizationType;
use Illuminate\Support\Collection;
use Livewire\Component;

class InstitutesSectionFilters extends Component
{
    public int $selectedFilter = 0;
    public Collection $types;


    public function mount() : void
    {
        $this->types = OrganizationType::all();
    }


    public function setFilter($id) : void
    {
        $this->selectedFilter = $id;
        $this->dispatch('filterSelected', $id);
    }

    public function render()
    {
        return view('livewire.institutes-section-filters');
    }
}
