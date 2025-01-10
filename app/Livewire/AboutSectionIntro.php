<?php

namespace App\Livewire;

use App\Settings\TeamSettings;
use Livewire\Component;

class AboutSectionIntro extends Component
{
    public function render()
    {
        return view('livewire.about-section-intro', [
            'teemSettings' => app(TeamSettings::class)
        ]);
    }
}
