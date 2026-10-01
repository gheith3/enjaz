<?php

namespace App\Livewire\Pages;

use App\Models\ImageMedia;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ImagesAlbumsPage extends Component
{
    use WithPagination;

    public null|string $searchQuery = null;

    public function search(): void
    {
        $this->searchQuery = str($this->searchQuery)->isNotEmpty()
            ? $this->searchQuery : null;
    }

    public function updatingSearchQuery(): void
    {
        $this->resetPage();
    }

    public function getList(): LengthAwarePaginator
    {
        return ImageMedia::query()
            ->where('is_active', true)
            ->when(str($this->searchQuery)->isNotEmpty(), function ($query) {
                return $query->where('title', 'like', '%' . $this->searchQuery . '%')
                    ->orWhere('description', 'like', '%' . $this->searchQuery . '%');
            })
            ->latest()
            ->paginate(20);
    }

    public function render(): View
    {
        return view('livewire.pages.images-albums-page', [
            'images' => $this->getList()
        ]);
    }
}
