<?php

namespace App\Livewire;

use Livewire\Component;

class AchieversSectionGrid extends Component
{
    public array $achievers = [
        [
            'name' => 'علي الحبسي',
            'category' => 'الرياضة',
            'image' => 'images/app/achieverExampleImg1.png'
        ],
        [
            'name' => 'د. جوخة الحارثية',
            'category' => 'الأدب',
            'image' => 'images/app/achieverExampleImg2.png'
        ],
        [
            'name' => 'محمد الحوسني',
            'category' => 'العلوم',
            'image' => 'images/app/achieverExampleImg3.png'
        ],
        [
            'name' => 'سارة البلوشي',
            'category' => 'الطبي',
            'image' => 'images/app/achieverExampleImg2.png'
        ],
        [
            'name' => 'أحمد الراشدي',
            'category' => 'العسكرية',
            'image' => 'images/app/achieverExampleImg3.png'
        ],
        [
            'name' => 'فاطمة الزدجالي',
            'category' => 'الفنون',
            'image' => 'images/app/achieverExampleImg1.png'
        ],
        [
            'name' => 'خالد الريامي',
            'category' => 'ريادة الأعمال',
            'image' => 'images/app/achieverExampleImg1.png'
        ],
        [
            'name' => 'مريم المعمري',
            'category' => 'الإبتكارات',
            'image' => 'images/app/achieverExampleImg3.png'
        ]
    ];

    public string $selectedFilter = 'الكل';

    protected $listeners = ['filterSelected' => 'updateFilter'];

    public function updateFilter($filter)
    {
        $this->selectedFilter = $filter;
    }

    public function getFilteredAchieversProperty()
    {
        if ($this->selectedFilter === 'الكل') {
            return $this->achievers;
        }
        
        return array_filter($this->achievers, function($achiever) {
            return $achiever['category'] === $this->selectedFilter;
        });
    }

    public function render()
    {
        return view('livewire.achievers-section-grid');
    }
}
