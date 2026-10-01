<?php

namespace App\Livewire\Components;

use App\Models\PublicFigure;
use Illuminate\View\View;
use Livewire\Component;

class SelectedAchiever extends Component
{
    public PublicFigure|null $person;

    public function mount() : void
    {
        $this->setPerson();
    }

    private function setPerson() : void
    {
        $this->person = PublicFigure::query()
            ->inRandomOrder()
            ->first();
    }

    public function render() : View
    {
        return view('livewire.components.selected-achiever');
    }
}
