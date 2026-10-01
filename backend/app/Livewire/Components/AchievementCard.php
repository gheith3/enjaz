<?php

namespace App\Livewire\Components;

use App\Models\Achievement;
use Illuminate\View\View;
use Livewire\Component;

class AchievementCard extends Component
{
    public Achievement $achievement;

    public function render() : View
    {
        return view('livewire.components.achievement-card');
    }
}
