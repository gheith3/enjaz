<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;

class ProfileSlider extends Component
{

    public Collection $profiles;


    public function render()
    {
        return view('livewire.profile-slider');
    }
}
