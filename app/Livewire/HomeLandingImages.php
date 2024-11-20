<?php

namespace App\Livewire;

use App\Models\LandingImage;
use Livewire\Component;

class HomeLandingImages extends Component
{
    public LandingImage|null $image;
    public function mount(): void {
        $this->setImages();
    }

    public function setImages() : void
    {
        $this->image = LandingImage::query()
            ->where('is_active', true)
            ->first();
    }

    public function render()
    {
        return view('livewire.home-landing-images');
    }
}
