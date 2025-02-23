<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Illuminate\View\View;

class HomePage extends Component
{
    public function render(): View
    {
        return view('livewire.pages.home-page');
    }
}
