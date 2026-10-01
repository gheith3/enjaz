<?php

namespace App\Livewire\Pages;

use App\Models\Achievement;
use Livewire\Component;

class AchievementShow extends Component
{
    public Achievement $achievement;

    public function mount(Achievement $achievement): void
    {
        abort_unless($achievement->is_published, 404);

        $achievement->loadMissing([
            'achievementMainType',
            'subAchievementType',
            'publicFigures' => fn ($query) => $query->published()->ordered(),
        ]);

        $this->achievement = $achievement;
    }

    public function render()
    {
        $related = Achievement::query()->published()
            ->with(['achievementMainType', 'subAchievementType'])
            ->when(
                $this->achievement->achievement_main_type_id,
                fn ($query) => $query->where('achievement_main_type_id', $this->achievement->achievement_main_type_id)
            )
            ->whereKeyNot($this->achievement->getKey())
            ->ordered()
            ->latest('achieved_at')
            ->take(3)
            ->get();

        return view('livewire.pages.achievement-show', [
            'related' => $related,
            'achiever' => $this->achievement->publicFigures->first(),
        ])->layout('layouts.site');
    }
}
