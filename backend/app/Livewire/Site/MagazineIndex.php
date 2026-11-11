<?php

namespace App\Livewire\Site;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Support\ArabicSearch;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * كل المقالات (articles.html #mag-all): tabs by category and the search of the hero (which sends "mag-search"),
 * like the design's js/articles-page.js. The issue's lead article is shown above, so it is not repeated here.
 */
class MagazineIndex extends Component
{
    #[Locked]
    public ?int $exceptId = null;

    #[Url(except: '')]
    public string $tab = '';

    #[Url(except: '')]
    public string $q = '';

    public function setTab(string $slug): void
    {
        $this->tab = $this->categories->contains('slug', $slug) ? $slug : '';
    }

    #[On('mag-search')]
    public function search(string $q = ''): void
    {
        $this->q = mb_substr(trim($q), 0, 100);
    }

    /** @return Collection<int, ArticleCategory> */
    #[Computed]
    public function categories(): Collection
    {
        return ArticleCategory::query()->orderBy('sort_order')->orderBy('id')->get();
    }

    public function render(): View
    {
        $articles = Article::query()->listed()->with(['media', 'writer.media', 'category'])
            ->when($this->exceptId, fn ($q) => $q->whereKeyNot($this->exceptId))->get();
        $needle = ArabicSearch::normalize($this->q);
        $rows = $articles
            ->filter(fn (Article $a) => $this->tab === '' || $a->category?->slug === $this->tab)
            ->filter(fn (Article $a) => $needle === '' || str_contains(ArabicSearch::normalize(implode(' ', [
                $a->title, $a->category?->name, $a->minutesLabel(), $a->writer?->name,
            ])), $needle))
            ->values();

        return view('livewire.site.magazine-index', ['rows' => $rows]);
    }
}
