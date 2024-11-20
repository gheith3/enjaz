<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;

class PublicFigure extends Component
{
    public Collection $profiles;
    public array|null $profile;
    public string|null $selectedId = "123";

    public function mount(Collection $profiles)
    {
        $this->profiles = $profiles;
        $this->selectedId = $this->profiles->first()['id'];
        $this->setProfile();
    }

    protected function getListeners() : array
    {
        return [
            'slide-changed' => 'selectedIdChange',
        ];
    }

    public function selectedIdChange(string $selectedId) : void{
        $this->selectedId = $selectedId;
        $this->setProfile();
    }

    public function setProfile() : void
    {
        $this->profile = $this->profiles->where('id', $this->selectedId)->first();
    }
    public function render()
    {
        return view('livewire.public-figure');
    }
}
