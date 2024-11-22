<?php

namespace App\Livewire;

use Livewire\Component;

class AchieversSectionFilters extends Component
{
    public string $selectedFilter = 'الكل';

    public array $primaryFilters = [
        [
            'name' => 'الكل',
            'icon' => null,
            'active' => true
        ],
        [
            'name' => 'الثقافة',
            'icon' => 'images\app\cultureBookIcon.svg'
        ],
        [
            'name' => 'العلوم',
            'icon' => 'images\app\cultureBookIcon.svg'
        ],
        [
            'name' => 'العسكرية',
            'icon' => 'images\app\cultureBookIcon.svg'
        ],
        [
            'name' => 'الطبي',
            'icon' => 'images\app\cultureBookIcon.svg'
        ],
        [
            'name' => 'الرياضة',
            'icon' => 'images\app\cultureBookIcon.svg'
        ]
    ];

    public array $secondaryFilters = [
        [
            'name' => 'الأدب',
            'icon' => 'images\app\cultureBookIcon.svg'
        ],
        [
            'name' => 'طلبة المدارس',
            'icon' => 'images\app\cultureBookIcon.svg'
        ],
        [
            'name' => 'الفنون',
            'icon' => 'images\app\cultureBookIcon.svg'
        ],
        [
            'name' => 'ريادة الأعمال',
            'icon' => 'images\app\cultureBookIcon.svg'
        ],
        [
            'name' => 'الإبتكارات',
            'icon' => 'images\app\cultureBookIcon.svg'
        ]
    ];

    public function setFilter($filterName)
    {
        $this->selectedFilter = $filterName;
        $this->emit('filterSelected', $filterName);
    }

    public function render()
    {
        return view('livewire.achievers-section-filters');
    }
}
