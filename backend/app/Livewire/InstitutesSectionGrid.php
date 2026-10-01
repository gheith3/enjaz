<?php

namespace App\Livewire;

use App\Models\Organization;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class InstitutesSectionGrid extends Component
{
    public Collection $institutes;
    public int $selectedFilter = 0;
    public string|null $searchFilter = null;

//    protected $listeners = ['filterSelected' => 'updateFilter'];

    public function mount(): void
    {
        $this->setInstitutes();
    }

    public function setInstitutes(): void
    {
        $this->institutes = Organization::query()
            ->when($this->selectedFilter !== 0, function ($query) {
                return $query->where('organization_type_id', $this->selectedFilter);
            })
            ->when(str($this->searchFilter)->isNotEmpty(), function ($query) {
                return $query->where(function ($query) {
                    $query->where('name_ar', 'like', '%' . $this->searchFilter . '%')
                        ->orWhere('name_en', 'like', '%' . $this->searchFilter . '%');
                });
            })
            ->get();
    }

    #[On('filterSelected')]
    public function onFilterSelected($filter): void
    {
        $this->selectedFilter = $filter;
        $this->setInstitutes();
    }

    #[On('filterSearch')]
    public function onFilterSearch($filter): void
    {
        $this->searchFilter = $filter;
        $this->setInstitutes();
    }


    public function render()
    {
        return view('livewire.institutes-section-grid');
    }
}
