<?php

namespace App\Livewire\Pages;

use App\Models\Achievement;
use App\Models\AchievementMainType;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class AchievementsPage extends Component
{
    public null|string $searchQuery = null;
    public null|int $main = null;
    public null|int $sub = null;
    public string $selected = 'all';
    public Collection $achievements;

    public function mount(): void
    {
        $this->setAchievements();
    }

    public function search(): void
    {
        $this->searchQuery = str($this->searchQuery)->isNotEmpty()
            ? $this->searchQuery : null;
        $this->setAchievements();
    }

    public function setType($key): void
    {
        $this->selected = $key;
        if ($key == 'all') {
            $this->main = null;
            $this->sub = null;
            $this->setAchievements();
            return;
        }

        $keys = str($key)->explode('-');
        $this->main = null;
        $this->sub = null;

        if ($keys[0] == 'mt') {
            $this->main = $keys[1];
        } else {
            $this->main = $keys[1];
            $this->sub = $keys[2];
        }
        $this->setAchievements();
    }

    public function getCategories(): array
    {
        $list = [];
        $list['all'] = 'الكل';
        foreach (AchievementMainType::query()->with('subTypes')->get() as $type) {
            $list['mt-' . $type->id . '-00'] = $type->title_ar;
            foreach ($type->subTypes as $subType) {
                $list["st-{$type->id}-{$subType->id}"] = $subType->title_ar;
            }
        }
        return $list;
    }

    public function setAchievements(): void
    {
        $this->achievements = Achievement::query()
            ->latest()
            ->when(str($this->searchQuery)->isNotEmpty(), function ($query) {
                return $query->where('title_ar', 'like', '%' . $this->searchQuery . '%')
                    ->orWhere('title_en', 'like', '%' . $this->searchQuery . '%');
            })
            ->when($this->main != null, function ($query) {
                return $query->where('achievement_main_type_id', $this->main)
                    ->when($this->sub, function ($query) {
                        return $query->where('sub_achievement_type_id', $this->sub);
                    });
            })
            ->with(
                'achievementScope',
                'achievementMainType',
                'subAchievementType',
            )
            ->get();
    }

    public function render(): View
    {
        return view('livewire.pages.achievements-page');
    }
}
