<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;

class PublicFigure extends Component
{

    public \App\Models\PublicFigure|null $person;

    public function mount() : void
    {
        $this->setPerson();
    }

    private function setPerson() : void
    {
        $this->person = \App\Models\PublicFigure::query()
            ->inRandomOrder()
            ->first();
    }


    public function render()
    {
        return view('livewire.public-figure');
    }


}
