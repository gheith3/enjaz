<?php

namespace App\Livewire\Site;

use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\Article;
use App\Models\Award;
use App\Models\LabItem;
use App\Models\News;
use App\Models\Organization;
use App\Support\ArabicSearch;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * البحث في الموقع (/search?q=, the menu's search box). The design has no search page: results reuse the design's cards
 * (achievers, achievements) and the magazine rows (the rest). Search ignores hamza, ta marbuta and diacritics.
 */
class SearchPage extends Component
{
    public const LIMIT = 12;

    #[Url(except: '')]
    public string $q = '';

    public function render(): View
    {
        $term = trim(mb_substr($this->q, 0, 100));
        $groups = [];
        if (mb_strlen($term) >= 2) {
            $find = fn ($query, array $columns) => ArabicSearch::where($query, $columns, $term)->limit(self::LIMIT)->get();
            $groups = array_filter([
                'achievers' => $find(Achiever::query()->published()->with(['media', 'field']), ['name', 'headline']),
                'achievements' => $find(Achievement::query()->published()->with(['media', 'achievers.media', 'field', 'scope']), ['title', 'excerpt']),
                'organizations' => $find(Organization::query()->published()->with('media'), ['name', 'short_name']),
                'awards' => $find(Award::query()->published()->with('media'), ['name']),
                'lab' => $find(LabItem::query()->published()->with('media'), ['title', 'short', 'who']),
                'articles' => $find(Article::query()->published()->with(['media', 'writer']), ['title', 'dek']),
                'news' => $find(News::query()->published()->with('media'), ['title', 'summary']),
            ], fn ($c) => $c->isNotEmpty());
        }

        return view('livewire.site.search-page', ['groups' => $groups, 'term' => $term]);
    }
}
