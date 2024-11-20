<?php

namespace App\Livewire;

use Livewire\Component;

class Achievements extends Component
{
    public array $categoriz = [
        "الرياضة",
        "الثقافة",
        "الإبتكارات",
    ];

    public function render()
    {
        return view('livewire.achievements');
    }
}
