<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Achiever;
use App\Models\Page;
use Illuminate\Contracts\View\View;

class AchieverController extends Controller
{
    public function index(): View
    {
        return view('site.pages.achievers', ['page' => Page::forKey('achievers')]);
    }

    /**
     * Drafts are visible only to dashboard users (preview); visitors get 404.
     */
    public function show(Achiever $achiever): View
    {
        abort_unless($achiever->isLive() || auth()->user()?->can('View:Achiever'), 404);

        $achiever->load(['field', 'media', 'journeyItems', 'quotes.media', 'socialLinks' => fn ($q) => $q->where('is_active', true)]);
        $achievements = $achiever->publishedAchievements()->with(['field', 'scope', 'media', 'achievers'])->get();
        $achiever->setRelation('publishedAchievements', $achievements);

        return view('site.pages.achiever', [
            'achiever' => $achiever,
            'achievements' => $achievements,
            'stats' => $achiever->medalStats(),
            'related' => $achiever->related(),
            'listPage' => Page::forKey('achievers'),
        ]);
    }
}
