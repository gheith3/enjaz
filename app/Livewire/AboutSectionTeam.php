<?php

namespace App\Livewire;

use Livewire\Component;

class AboutSectionTeam extends Component
{
    public array $teamMembers = [
        [
            'image' => 'images/app/ceo.png',
            'name' => 'عضو الفريق الأول',
            'title' => 'المنصب الأول'
        ],
        [
            'image' => 'images/app/ceo.png',
            'name' => 'عضو الفريق الثاني',
            'title' => 'المنصب الثاني'
        ],
        [
            'image' => 'images/app/ceo.png',
            'name' => 'عضو الفريق الثالث',
            'title' => 'المنصب الثالث'
        ],
        [
            'image' => 'images/app/ceo.png',
            'name' => 'عضو الفريق الرابع',
            'title' => 'المنصب الرابع'
        ],
        [
            'image' => 'images/app/ceo.png',
            'name' => 'عضو الفريق الخامس',
            'title' => 'المنصب الخامس'
        ],
        [
            'image' => 'images/app/ceo.png',
            'name' => 'عضو الفريق السادس',
            'title' => 'المنصب السادس'
        ],
        [
            'image' => 'images/app/ceo.png',
            'name' => 'عضو الفريق السابع',
            'title' => 'المنصب السابع'
        ],
        [
            'image' => 'images/app/ceo.png',
            'name' => 'عضو الفريق الثامن',
            'title' => 'المنصب الثامن'
        ],
    ];

    public function render()
    {
        return view('livewire.about-section-team');
    }
}