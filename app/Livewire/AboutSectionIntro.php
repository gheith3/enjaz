<?php

namespace App\Livewire;

use Livewire\Component;

class AboutSectionIntro extends Component
{
    public string $title = 'من نحن';
    public string $description = 'من منطلقِ الواجب الوطني، وفخرًا بهذا الوطن المعطاء، وإيمانًا بدور الشباب في تحقيق رؤية المملكة 2030، نسعى في إنجاز إلى تمكين الشباب وتأهيلهم ليكونوا قادة المستقبل.';
    
    public array $images = [
        'about-1.jpg',
        'about-2.jpg',
        'about-3.jpg',
        'about-4.jpg'
    ];

    public function render()
    {
        return view('livewire.about-section-intro');
    }
} 