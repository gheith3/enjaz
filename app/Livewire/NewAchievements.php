<?php

namespace App\Livewire;

use App\Models\Achievement;
use Illuminate\Support\Collection;
use Livewire\Component;

class NewAchievements extends Component
{
    public Collection $achievements;

    public int|null $achievementMainTypeId = null;
    public int|null $activeAchievement = null;
    public null|string $sectionTitle = null;

    public function mount($achievementMainTypeId = null, $activeAchievement = null, $sectionTitle = null): void
    {
        $this->achievementMainTypeId = $achievementMainTypeId;
        $this->activeAchievement = $activeAchievement;
        $this->sectionTitle = $sectionTitle ?? "أحدث الإنجازات";
        $this->setAchievementsList();
    }

    public function setAchievementsList(): void
    {
        $this->achievements = Achievement::query()
            ->latest()
            ->limit(4)
            ->when($this->achievementMainTypeId, function ($query) {
                return $query->where('achievement_main_type_id', $this->achievementMainTypeId);
            })
            ->when($this->activeAchievement, function ($query) {
                return $query->where('id', '!=', $this->activeAchievement);
            })
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
