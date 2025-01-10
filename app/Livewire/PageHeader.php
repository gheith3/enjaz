<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class PageHeader extends Component
{
    public null|string $title;
    public null|string $subTitle;
    public null|string $description;
    public null|string $backgroundImage;
    public null|string $headerImage;


    public function render() : View
    {
        return view('livewire.page-header');
    }
}
