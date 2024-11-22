<?php

namespace App\Livewire;

use Livewire\Component;

class AboutSectionMedia extends Component
{
    public array $mediaItems = [
        [
            'type' => 'profile',
            'title' => 'الملف التعريفي',
            'icon' => 'images/app/resume.svg',
            'background' => 'gradient'
        ],
        [
            'type' => 'interviews',
            'title' => 'لقاءات مسجلة',
            'icon' => 'images/app/group-38068.svg',
            'background' => 'images/app/group-38072.png'
        ],
        [
            'type' => 'photos',
            'title' => 'صور متنوعة',
            'icon' => 'images/app/group-38067.svg',
            'background' => 'images/app/group-38077.png'
        ],
        [
            'type' => 'book2',
            'title' => 'الاصدار الثاني',
            'icon' => 'images/app/group-38066.svg',
            'image' => 'images/app/book-cover-2.png'
        ],
        [
            'type' => 'book1',
            'title' => 'الإصدار الأول',
            'icon' => 'images/app/group-38066.svg',
            'image' => 'images/app/book-cover-1.png'
        ],
    ];

    public function render()
    {
        return view('livewire.about-section-media');
    }
}