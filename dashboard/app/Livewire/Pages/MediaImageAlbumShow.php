<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Models\ImageMedia;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.site')]
class MediaImageAlbumShow extends Component
{
    public ImageMedia $imageMedia;

    public function mount(ImageMedia $imageMedia): void
    {
        abort_unless($imageMedia->is_published && $imageMedia->is_active, 404);

        $this->imageMedia = $imageMedia;
    }

    public function render()
    {
        return view('livewire.pages.media-image-album-show');
    }
}
