<?php

namespace App\Livewire;

use App\Models\Achievement;
use App\Models\AchievementMainType;
use App\Models\SubAchievementType;
use Illuminate\View\View;
use Livewire\Component;

class Statics extends Component
{

    public function getStatics() : array
    {
        return [
            [
                'title' => 'المنجزين',
                'icon' => asset("images/achievement.svg"),
                'count' => \App\Models\PublicFigure::count(),
            ],
            [
                'title' => 'الإنجازات',
                'icon' => asset("images/badge.svg"),
                'count' => Achievement::count(),
            ],
            [
                'title' => 'المجالات',
                'icon' => asset("images/categories.svg"),
                'count' => AchievementMainType::count() + SubAchievementType::count(),
            ]
        ];
    }
    public function render() : View
    {
        return view('livewire.statics');
    }
}
