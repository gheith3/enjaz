<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Page;
use Illuminate\Contracts\View\View;

class NewsController extends Controller
{
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
