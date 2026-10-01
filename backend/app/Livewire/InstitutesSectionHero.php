<?php

namespace App\Livewire;

use Livewire\Component;

class InstitutesSectionHero extends Component
{
    public string $backgroundImage = 'images/app/ABG.svg';
    public string|null $searchKey = null;

    public function search() : void
    {
        $this->dispatch('filterSearch', $this->searchKey);
    }

    public function render()
    {
        return view('livewire.institutes-section-hero');
    }
}
