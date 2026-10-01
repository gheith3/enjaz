<?php

namespace App\Livewire\Pages;

use App\Models\News;
use Livewire\Component;

class NewsShow extends Component
{
    public News $news;

    public function mount(News $news): void
    {
        abort_unless($news->is_published, 404);

        $news->loadMissing('organization');

        $this->news = $news;
    }

    public function render()
    {
        $related = News::query()->published()
            ->whereKeyNot($this->news->getKey())
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('livewire.pages.news-show', [
            'related' => $related,
        ])->layout('layouts.site');
    }
}
