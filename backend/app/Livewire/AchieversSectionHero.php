<?php

namespace App\Livewire;

use Livewire\Component;

class AchieversSectionHero extends Component
{
    public string $backgroundImage = 'images/app/AchieversBG.svg';
    public string $decorationImage = 'images/app/ABG.svg';
    public string $searchQuery = '';


    public function search() : void
    {
        $this->dispatch('searchUpdated', [
            'search-query' => $this->searchQuery,
        ]);
    }

    public function render()
    {
        return view('livewire.achievers-section-hero');
    }
}
