<?php

namespace App\Livewire\Pages;


use App\Models\VideoMedia;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class VideosPage extends Component
{
    public function getList() : Collection
    {
        return VideoMedia::query()
            ->where('is_active', true)
            ->get();
    }

    public function render() : View
    {
        return view('livewire.pages.videos-page');
    }
}
