<?php

namespace App\Livewire\Pages;

use App\Settings\CompanySettings;
use Illuminate\View\View;
use Livewire\Component;

class MediaPage extends Component
{
    public function render() : View
    {
        return view('livewire.pages.media-page')
            ->with([
                'settings' => app(CompanySettings::class)
            ]);
    }
}
