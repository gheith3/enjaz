<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;

class Profiles extends Component
{
    public Collection $profiles;

    public function mount()
    {
        $this->setProfiles();
    }

    public function setProfiles(): void {
        $this->profiles =  collect([
            [
                'id' => 1,
                'src' => asset("images/swiper/teddy1.png"),
            ],
            [
                'id' => 2,
                'src' => asset("images/swiper/teddy2.png"),
            ],
            [
                'id' => 3,
                'src' => asset("images/swiper/teddy3.png"),
            ],
            [
                'id' => 4,
                'src' => asset("images/swiper/teddy4.png"),
            ],
            [
                'id' => 5,
                'src' => asset("images/swiper/teddy5.png"),
            ],
            [
                'id' => 6,
                'src' => asset("images/swiper/teddy6.jpg"),
            ],
        ]);
    }



    public function render()
    {
        return view('livewire.profiles');
    }
}
