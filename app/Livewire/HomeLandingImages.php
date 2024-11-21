<?php

namespace App\Livewire;

use App\Models\LandingImage;
use Livewire\Component;
use Nette\Utils\Image;

class HomeLandingImages extends Component
{
    public array $image = [];
    public function mount(): void {
        $this->image = $this->getImages();

    }

    public function getImages() : array
    {
        $image = LandingImage::query()
            ->where('is_active', true)
            ->inRandomOrder()
            ->first();

        if($image) {
            return $image->toArray();
        }

        return [
            'image_path' => "images/app/hm.png",
            'image_alt' => "حضرة صاحب الجلالة السلطان هيثم حفظه الله ورعاه",
            'subtitle_ar' => "حضرة صاحب الجلالة السلطان هيثم حفظه الله ورعاه",
            'subtitle_en' => "حضرة صاحب الجلالة السلطان هيثم حفظه الله ورعاه",
            'description_ar' => "إن الشباب هم ثروة الأمم وموردها الذي لا ينضب وسواعدها التي تبني، هم حاضر الأمة ومستقبلها، وسوف نحرص على الاستماع لهم وتلمس احتياجاتهم واهتماماتهم وتطلعاتهم ولا شك أنها ستجد العناية التي تستحق",
            'description_en' => "إن الشباب هم ثروة الأمم وموردها الذي لا ينضب وسواعدها التي تبني، هم حاضر الأمة ومستقبلها، وسوف نحرص على الاستماع لهم وتلمس احتياجاتهم واهتماماتهم وتطلعاتهم ولا شك أنها ستجد العناية التي تستحق",
            'is_active' => true,
        ];
    }


    public function render()
    {
        return view('livewire.home-landing-images');
    }
}
