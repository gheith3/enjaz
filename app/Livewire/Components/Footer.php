<?php

namespace App\Livewire\Components;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class Footer extends Component
{
    public function getLinks(): Collection
    {
        return collect([
            'الصفحة الرئيسية' => route('home'),
            'عنا' => route('about'),
            'الإنجازات' => route('achievements'),
            'المؤسسات' => route('organizations'),
            'المنجزين' => route('achievers'),
            'الإعلام' => '#',
            'تواصل معنا' => route('contact-us'),
        ]);
    }

    public function getAddressDetails(): array
    {
        return [
            [
                'icon' => 'fas fa-map-marker-alt',
                'text' => '+968 93839229'
            ],
            [
                'icon' => 'fas fa-phone-email',
                'text' => 'info@enjaz.com'
            ],
            [
                'icon' => 'fas fa-map-marker-alt',
                'text' => 'مخافظة مسقط بوشر'
            ],
        ];
    }

    public function render(): View
    {
        return view('livewire.components.footer');
    }
}
