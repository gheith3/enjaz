<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Page;
use Illuminate\Contracts\View\View;

class AchievementController extends Controller
{
    public function index(): View
    {
        return view('site.pages.achievements', ['page' => Page::forKey('achievements')]);
    }

    /**
     * Drafts are visible only to dashboard users (preview); visitors get 404.
     */
    public function show(Achievement $achievement): View
    {
        abort_unless($achievement->isLive() || auth()->user()?->can('View:Achievement'), 404);

        $achievement->load(['field', 'scope', 'sources', 'media', 'achievers' => fn ($q) => $q->with(['media', 'field'])]);
        $achiever = $achievement->primaryAchiever();
        $path = $achiever ? $achiever->publishedAchievements()->with(['scope', 'field', 'media'])->reorder()->orderBy('achieved_at')->orderBy('achievements.id')->get() : collect();

        return view('site.pages.achievement', [
            'achievement' => $achievement,
            'achiever' => $achiever,
            'path' => $path,
            'others' => self::others($path, $achievement),
            'listPage' => Page::forKey('achievements'),
        ]);
    }

    /**
     * Three other achievements of the same achiever: the ones that follow this one in time (wrapping around).
     */
    private static function others($path, Achievement $current)
    {
        $index = $path->search(fn (Achievement $a) => $a->is($current));
        $rest = $index === false ? $path : $path->slice($index + 1)->concat($path->slice(0, $index));

        return $rest->reject(fn (Achievement $a) => $a->is($current))->take(3)->values();
    }
}
