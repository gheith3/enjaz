<?php

namespace App\Livewire;

use Livewire\Component;

class InstitutesSectionGrid extends Component
{
    public array $institutes = [
        [
            'image' => 'images/app/institute2.svg'
        ],
        [
            'image' => 'images/app/institute2.svg'
        ],
        [
            'image' => 'images/app/institute2.svg'
        ],
        [
            'image' => 'images/app/institute2.svg'
        ],
        [

            'image' => 'images/app/institute2.svg'
        ],
        [

            'image' => 'images/app/institute2.svg'
        ],
        [
  
            'image' => 'images/app/institute2.svg'
        ],
        [
        
            'image' => 'images/app/institute2.svg'
        ]
    ];

    public string $selectedFilter = 'الكل';

    protected $listeners = ['filterSelected' => 'updateFilter'];

    public function updateFilter($filter)
    {
        $this->selectedFilter = $filter;
    }

    public function getFilteredInstitutesProperty()
    {
        if ($this->selectedFilter === 'الكل') {
            return $this->institutes;
        }
        
        return array_filter($this->institutes, function($institute) {
            return $institute['category'] === $this->selectedFilter;
        });
    }

    public function render()
    {
        return view('livewire.institutes-section-grid');
    }
}
