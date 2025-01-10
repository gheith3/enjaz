<?php

namespace App\Livewire;


use App\Models\AchievementMainType;
use App\Models\PublicFigure;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class Achievers extends Component
{
    public int|null $achievementMainTypeId = null;

    public function mount($achievementMainTypeId = null): void
    {
        $this->achievementMainTypeId = $achievementMainTypeId;
    }


    public function getList(): array
    {
        $list = AchievementMainType::query()
            ->latest()
            ->whereHas('achievements', function ($query) {
                $query->whereHas('publicFigures');
            })
            ->when($this->achievementMainTypeId, function ($query) {
                $query->where('id', $this->achievementMainTypeId);
            })
            ->with('achievements.publicFigures')
            ->get();

        $items = [];

        foreach ($list as $entry) {
            $item = [];
            $item['category'] = $entry;
            $item['achievers'] = [];

            foreach ($entry->achievements as $achievement) {
                foreach ($achievement->publicFigures as $achiever) {
                    $item['achievers'][] = $achiever;
                }
            }
            $items[] = $item;
        }

        return $items;
    }

    public function render(): View
    {
        return view('livewire.achievers');
    }
}
