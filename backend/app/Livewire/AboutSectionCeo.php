<?php

namespace App\Livewire;

use App\Settings\TeamSettings;
use Livewire\Component;

class AboutSectionCeo extends Component
{
    public function render()
    {
        return view('livewire.about-section-ceo',
        ['teemSettings' => app(TeamSettings::class)]);
    }
}
