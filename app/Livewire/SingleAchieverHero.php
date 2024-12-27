<?php

namespace App\Livewire;

use App\Models\Achievement;
use Livewire\Component;

class SingleAchieverHero extends Component
{
    public $achiever;
    public int $achievements = 0;
    public int $types = 0;
    public function mount($achiever)
    {
        $this->achiever = \App\Models\PublicFigure::find($achiever);
        $this->setStatics();
    }

    private function setStatics() : void
    {
        if ($this->achiever == null)
            return;

        $this->achievements = Achievement::query()
            ->whereHas('publicFigures', function ($query) {
                $query->where('public_figure_id', $this->achiever->id);
            })
            ->count();

        $this->types = Achievement::query()
            ->whereHas('publicFigures', function ($query) {
                $query->where('public_figure_id', $this->achiever->id);
            })
            ->distinct('achievement_main_type_id')
            ->count('achievement_main_type_id');
    }

    public function render()
    {
        return view('livewire.single-achiever-hero');
    }
}
