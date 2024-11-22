<?php

namespace App\Livewire;

use Livewire\Component;

class InstitutesSection extends Component
{
    public array $institutes = [
        [
            'image' => 'images/app/instituteLogo1.png' // Replace with actual image paths
        ],
        [
            'image' => 'images/app/instituteLogo2.png'
        ],
        [
            'image' => 'images/app/instituteLogo3.png'
        ],
        // Add more institute logos as needed
    ];

    public string $selectedFilter = 'الكل';

    protected $listeners = ['filterSelected' => 'updateFilter'];

    public function updateFilter($filter)
    {
        $this->selectedFilter = $filter;
    }

    public function render()
    {
        return view('livewire.institutes-section');
    }
} 