<?php

namespace App\Livewire\Components;

use App\Settings\CompanySettings;
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
            'الإعلام' => route('media'),
            'تواصل معنا' => route('contact-us'),
        ]);
    }

    public function getAddressDetails(): array
    {
        $settings = app(CompanySettings::class);
        return [
            [
                'icon' => 'fas fa-map-marker-alt',
                'text' => $settings->phone,
                'link' => 'tel:' . $settings->phone,
            ],
            [
                'icon' => 'fas fa-phone-email',
                'text' => $settings->email,
                'link' => 'mailto:' . $settings->email,
            ],
            [
                'icon' => 'fas fa-map-marker-alt',
                'text' => $settings->address,
                'link' => null,
            ],
        ];
    }

    public function render(): View
    {
        return view('livewire.components.footer')
            ->with([
                'settings' => app(CompanySettings::class),
            ]);
    }
}
