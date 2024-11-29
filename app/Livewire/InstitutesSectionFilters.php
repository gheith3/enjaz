<?php

namespace App\Livewire;

use Livewire\Component;

class InstitutesSectionFilters extends Component
{
    public string $selectedFilter = 'الكل';

    public array $primaryFilters = [
        [
            'name' => 'الكل',
            'active' => true
        ],
        [
            'name' => 'خاص',
        ],
        [
            'name' => 'حكومي',
        ]
    ];


    public function setFilter($filterName)
    {
        $this->selectedFilter = $filterName;
        $this->emit('filterSelected', $filterName);
    }

    public function render()
    {
        return view('livewire.institutes-section-filters');
    }
}
