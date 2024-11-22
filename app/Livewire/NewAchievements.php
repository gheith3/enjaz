<?php

namespace App\Livewire;

use App\Models\Achievement;
use Illuminate\Support\Collection;
use Livewire\Component;

class NewAchievements extends Component
{
    public Collection $achievements;

    public function mount() : void
    {
        $this->setAchievementsList();
    }

    public function setAchievementsList() : void
    {
        $this->achievements = Achievement::query()
            ->latest()
            ->limit(4)
            ->with(
                'achievementScope',
                'achievementMainType',
                'subAchievementType',
            )
            ->get();
    }

    public function render()
    {
        return view('livewire.new-achievements');
    }
}
