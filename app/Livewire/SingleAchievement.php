<?php

namespace App\Livewire;

use App\Models\Achievement;
use Illuminate\View\View;
use Livewire\Component;

class SingleAchievement extends Component
{
    public Achievement $achievement;

    public function mount(Achievement $achievement) : void
    {
        $this->achievement = $achievement;
    }

    public function render() : View
    {
        return view('livewire.single-achievement');
    }
}
