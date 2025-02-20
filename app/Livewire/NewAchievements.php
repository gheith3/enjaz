<?php

namespace App\Livewire;

use App\Models\Achievement;
use Illuminate\Support\Collection;
use Livewire\Component;

class NewAchievements extends Component
{
    public Collection $achievements;

    public bool $showAll = false;
    public int|null $achievementMainTypeId = null;
    public int|null $activeAchievement = null;
    public int|null $instituteId = null;
    public int|null $achieverId = null;
    public null|string $sectionTitle = null;

    public function mount($achievementMainTypeId = null, $activeAchievement = null,
                          $sectionTitle = null, $instituteId = null,
                          $achieverId = null): void
    {
        $this->achievementMainTypeId = $achievementMainTypeId;
        $this->activeAchievement = $activeAchievement;
        $this->sectionTitle = $sectionTitle ?? "أحدث الإنجازات";
        $this->instituteId = $instituteId;
        $this->achieverId = $achieverId;
        $this->setAchievementsList();
    }

    public function setAchievementsList(): void
    {
        $this->achievements = Achievement::query()
            ->latest()
            ->when(!$this->showAll, function ($query) {
                return $query->limit(4);
            })
            ->when($this->achievementMainTypeId, function ($query) {
                return $query->where('achievement_main_type_id', $this->achievementMainTypeId);
            })
            ->when($this->activeAchievement, function ($query) {
                return $query->where('id', '!=', $this->activeAchievement);
            })
            ->when($this->instituteId, function ($query) {
                return $query->whereHas('organizations', function ($query) {
                    $query->where('organization_id', $this->instituteId);
                });
            })
            ->when($this->achieverId, function ($query) {
                return $query->whereHas('publicFigures', function ($query) {
                    $query->where('public_figure_id', $this->achieverId);
                });
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
