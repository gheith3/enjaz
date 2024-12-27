<?php

namespace App\Livewire;

use App\Models\Achievement;
use App\Models\AchievementMainType;
use Illuminate\Support\Collection;
use Livewire\Component;

class Achievers extends Component
{
    public Collection $publicFigures;
    public int|null $achievementMainTypeId = null;

    public function mount($achievementMainTypeId = null): void
    {
        $this->achievementMainTypeId = $achievementMainTypeId;
        $this->setAchievementsList();
    }

    public function setAchievementsList(): void
    {
        //select AchievementMainType whether at list has 1 achievement
        //and group by achievement_main_type_id use AchievementMainType model
        //and get all achievements that has achievement_main_type_id

        $this->publicFigures = AchievementMainType::has('achievements')
//            ->where('achievements', '>', 1)
            ->when($this->achievementMainTypeId, function ($query) {
                return $query->where('id', $this->achievementMainTypeId);
            })
            ->with([
                'achievements', 'achievements.subAchievementType',
                'achievements.publicFigures', 'achievements.organizations'
            ])
            ->get();
    }

    public function render()
    {
        return view('livewire.achievers');
    }
}
