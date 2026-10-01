<?php

namespace App\Livewire\Pages;

use App\Models\ImageMedia;
use Illuminate\View\View;
use Livewire\Component;

class ImagesAlbumPage extends Component
{
    public ImageMedia $album;

    public function mount($id) : void
    {
        $this->album = ImageMedia::query()
            ->where('id', $id)
            ->firstOrFail();
    }


    public function render() : View
    {
        return view('livewire.pages.images-album-page');
    }
}
