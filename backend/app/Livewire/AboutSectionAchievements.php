<?php

namespace App\Livewire;

use Livewire\Component;

class AboutSectionAchievements extends Component
{
    public array $achievements = [
        [
            'image' => 'images/app/ceo.png',
            'date' => '22 May',
            'text' => 'إقامة حفل تدشين الكتاب بمجلس عُمان تحت رعاية معالي أمين عام شؤون البلاط السلطاني'
        ],
        [
            'image' => 'images/app/ceo.png',
            'date' => '15 June',
            'text' => 'تنظيم ملتقى الشباب العماني للإبداع والابتكار في مركز عُمان للمؤتمرات'
        ],
        [
            'image' => 'images/app/ceo.png',
            'date' => '3 July',
            'text' => 'إطلاق مبادرة تطوير المهارات القيادية للشباب العماني في القطاع الخاص'
        ],
        [
            'image' => 'images/app/ceo.png',
            'date' => '18 August',
            'text' => 'تنظيم معرض إنجازات الشباب العماني في مجال التكنولوجيا والابتكار'
        ],
        [
            'image' => 'images/app/ceo.png',
            'date' => '10 September',
            'text' => 'إقامة ورشة عمل لتطوير مهارات ريادة الأعمال للشباب العماني'
        ],
    ];

    public function render()
    {
        return view('livewire.about-section-achievements');
    }
}