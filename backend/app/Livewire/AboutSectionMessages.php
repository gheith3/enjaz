<?php

namespace App\Livewire;

use App\Settings\TeamSettings;
use Livewire\Component;

class AboutSectionMessages extends Component
{
    public array $messages = [];


    public function mount() : void
    {
        $teemSettings = app(TeamSettings::class);
       $this->messages = [
            [
                'icon' => 'images/app/vision.svg',
                'title' => 'الرؤية',
                'description' => $teemSettings->vision
            ],
            [
                'icon' => 'images/app/msg.svg',
                'title' => 'الرسالة',
                'description' => $teemSettings->message
            ],
            [
                'icon' => 'images/app/values.svg',
                'title' => 'القيم',
                'description' => $teemSettings->values
            ]
        ];
    }

    public function render()
    {
        return view('livewire.about-section-messages');
    }
}
