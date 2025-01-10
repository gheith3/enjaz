<?php

namespace App\Livewire;

use App\Settings\TeamSettings;
use Livewire\Component;

class AboutSectionTeam extends Component
{

    public function render()
    {
        return view('livewire.about-section-team',
        ['teemSettings' => app(TeamSettings::class)]);
    }
}
