<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Traits\InvalidatesPageCache;
use Livewire\Component;
use Illuminate\View\View;
use App\Models\Achievement;
use App\Models\PublicFigure;


class SingleAchieverHero extends Component
{
    use InvalidatesPageCache;

    public $achiever;
    public int $achievements = 0;
    public int $types = 0;
    public function mount($achiever) : void
    {
        $this->achiever = PublicFigure::findOrFail($achiever);
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

    public function render() : View
    {
        return view('livewire.single-achiever-hero');
    }

    public function updated($property): void
    {
        $this->clearPageCache();
    }
}
