<?php

namespace App\Livewire;

use Livewire\Component;

class InstitutesSectionHero extends Component
{
    public string $backgroundImage = 'images/app/ABG.svg';

    public function render()
    {
        return view('livewire.institutes-section-hero');
    }
}
