<?php

namespace App\Livewire;

use App\Models\Achievement;
use Livewire\Component;

class SingleAchievement extends Component
{
    public Achievement $achievement;

    public function mount(Achievement $achievement)
    {
        $this->achievement = $achievement;
    }

    public function render()
    {
        return view('livewire.single-achievement');
    }
}
