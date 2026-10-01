<?php

namespace App\Livewire\Components;

use App\Models\Achiever;
use App\Models\PublicFigure;
use Illuminate\View\View;
use Livewire\Component;

class AchieverCard extends Component
{
    public PublicFigure $achiever;
    public function render() : View
    {
        return view('livewire.components.achiever-card');
    }
}
