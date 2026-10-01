<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Models\VideoMedia;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.site')]
class MediaVideos extends Component
{
    use WithPagination;

    #[Url]
    public string $q = '';

    public function updatedQ(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $videos = VideoMedia::query()->published()->active()
            ->when($this->q !== '', fn (Builder $query) => $query->where(fn (Builder $q) => $q
                ->where('title_ar', 'like', "%{$this->q}%")
                ->orWhere('title_en', 'like', "%{$this->q}%")))
            ->ordered()
            ->paginate(12);

        return view('livewire.pages.media-videos', [
            'videos' => $videos,
        ]);
    }
}
