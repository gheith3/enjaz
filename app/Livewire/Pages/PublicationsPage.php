<?php

namespace App\Livewire\Pages;

use App\Models\PublicationMedia;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class PublicationsPage extends Component
{
    public function getList() : Collection
    {
        return PublicationMedia::query()
            ->where('is_active', true)
            ->get();
    }
    public function render() : View
    {
        return view('livewire.pages.publications-page');
    }
}
