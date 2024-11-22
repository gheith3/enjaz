<?php

namespace App\Livewire;

use App\Models\AchievementMainType;
use Illuminate\Support\Collection;
use Livewire\Component;

class Achievements extends Component
{
    public Collection $achivments;

    public function mount() : void
    {
        $this->setAchievementsList();
    }

    public function setAchievementsList() : void
    {
        //select AchievementMainType whether at list has 1 achievement
        //and group by achievement_main_type_id use AchievementMainType model
        //and get all achievements that has achievement_main_type_id
        $this->achivments = AchievementMainType::has('achievements')
//            ->where('achievements', '>', 1)
            ->with('achievements', 'achievements.subAchievementType')
            ->get();
    }

    public function render()
    {
        return view('livewire.achievements');
    }
}
