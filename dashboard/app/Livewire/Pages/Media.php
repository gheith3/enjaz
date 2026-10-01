<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Models\News;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.site')]
class Media extends Component
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
        $news = News::query()->published()
            ->when($this->q !== '', fn (Builder $query) => $query->where(fn (Builder $q) => $q
                ->where('title_ar', 'like', "%{$this->q}%")
                ->orWhere('title_en', 'like', "%{$this->q}%")))
            ->latest('published_at')
            ->paginate(8);

        return view('livewire.pages.media', [
            'news' => $news,
        ]);
    }
}
