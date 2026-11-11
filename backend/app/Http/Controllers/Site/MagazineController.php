<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Issue;
use App\Models\Page;
use App\Models\Writer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class MagazineController extends Controller
{
    /** المقالات (articles.html): hero, issue mast, lead article, quote, all articles (Livewire), most read, writers. */
    public function index(): View
    {
        $issue = Issue::current();
        $lead = $issue?->leadArticle?->isLive() ? $issue->leadArticle->load(['media', 'writer.media', 'category']) : null;

        return view('site.magazine.index', [
            'page' => Page::forKey('magazine'),
            'issue' => $issue,
            'lead' => $lead,
            'wall' => Article::query()->listed()->with('media')->limit(12)->get()->filter(fn (Article $a) => $a->hasMedia('cover'))->values(),
            'mostRead' => Article::query()->published()->with('writer')->orderByDesc('views')->orderByDesc('published_at')->limit(4)->get(),
            'writers' => $this->listedWriters(),
        ]);
    }

    /** One article (article.html). Drafts: preview for dashboard users only. */
    public function show(Article $article): View
    {
        abort_unless($article->isLive() || auth()->user()?->can('View:Article'), 404);
        request()->attributes->set('menu_section', route('magazine.index', absolute: false));
        $article->load(['media', 'writer.media', 'category', 'issue']);

        $others = Article::query()->listed()->with(['media', 'writer.media', 'category'])->whereKeyNot($article->getKey())->limit(20)->get();
        $related = $others->sortBy(fn (Article $a) => $a->article_category_id === $article->article_category_id ? 0 : 1)->take(2)->values();

        return view('site.magazine.show', [
            'page' => Page::forKey('magazine'),
            'article' => $article,
            'isLead' => $article->issue?->lead_article_id === $article->id,
            'related' => $related,
        ]);
    }

    /** أقلام إنجازات (writers.html). */
    public function writers(): View
    {
        $writers = $this->listedWriters();
        request()->attributes->set('menu_section', route('magazine.index', absolute: false));

        return view('site.magazine.writers', [
            'page' => Page::forKey('writers'),
            'issue' => Issue::current(),
            'writers' => $writers,
            'featured' => $writers->firstWhere('is_featured', true),
            'categories' => ArticleCategory::query()->orderBy('sort_order')->orderBy('id')->get(),
            'articlesCount' => $writers->sum(fn (Writer $w) => $w->articles->count()),
        ]);
    }

    /** Counts a read (sent by the article page once it is open; the page itself is cached). */
    public function view(Article $article): Response
    {
        if ($article->isLive()) {
            Article::withoutTimestamps(fn () => $article->newQuery()->whereKey($article->getKey())->increment('views'));
        }

        return response()->noContent();
    }

    /**
     * Published writers with their published articles (newest first); the writer of the newest article first.
     *
     * @return Collection<int, Writer>
     */
    private function listedWriters(): Collection
    {
        return Writer::query()->published()->with(['media', 'category', 'articles' => fn ($q) => $q->listed()])->get()
            ->sortByDesc(fn (Writer $w) => $w->articles->first()?->published_at?->timestamp ?? 0)->values();
    }
}
