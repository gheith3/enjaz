<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /** الأخبار (news.html): the news of one category (?category=slug, default the first), read in full. */
    public function index(Request $request): View
    {
        $categories = NewsCategory::query()->orderBy('sort_order')->orderBy('id')->get();
        $category = $categories->firstWhere('slug', $request->query('category')) ?? $categories->first();
        $page = Page::forKey('news');

        return view('site.pages.news-index', [
            'page' => $page,
            'categories' => $categories,
            'category' => $category,
            'items' => News::query()->published()->with('media')
                ->when($category, fn ($q) => $q->where('news_category_id', $category->id), fn ($q) => $q->whereNull('organization_id'))
                ->orderByDesc('happened_at')->orderBy('sort_order')->orderByDesc('id')->get(),
        ]);
    }

    /** Design org-news.html. Drafts: preview for dashboard users only. */
    public function show(News $news): View
    {
        abort_unless($news->isLive() || auth()->user()?->can('View:News'), 404);
        $news->load(['organization.media', 'organization.type', 'media']);

        return view('site.pages.news', [
            'news' => $news,
            'listPage' => Page::forKey('organizations'),
            'more' => News::query()->published()->with('media')->whereKeyNot($news->getKey())
                ->when($news->organization_id, fn ($q) => $q->where('organization_id', $news->organization_id))
                ->orderByDesc('happened_at')->limit(4)->get(),
        ]);
    }
}
