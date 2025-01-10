<?php

namespace App\Livewire\Pages;

use App\Models\ImageMedia;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class ImagesAlbumsPage extends Component
{
    public function getList() : Collection
    {
        return ImageMedia::query()
            ->where('is_active', true)
            ->get();
    }

    public function render() : View
    {
        return view('livewire.pages.images-albums-page');
    }
}
