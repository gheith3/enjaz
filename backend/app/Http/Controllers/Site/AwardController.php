<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Award;
use App\Models\Page;
use Illuminate\Contracts\View\View;

class AwardController extends Controller
{
    /** جوائز السلطان قابوس (awards.html): the awards that have a card style, with the page hero. */
    public function index(): View
    {
        return view('site.pages.awards', [
            'page' => Page::forKey('awards')->load('media'),
            'awards' => Award::query()->listed()->whereNotNull('card_style')->with(['media', 'editions.winners'])->get(),
        ]);
    }

    /** One route for every award; the kind picks the design (culture, volunteer, architecture). */
    public function show(Award $award): View
    {
        abort_unless($award->isLive() || auth()->user()?->can('View:Award'), 404);
        // Awards with a card belong to "جوائز السلطان قابوس"; the others (Bilarab) have their own menu link.
        request()->attributes->set('menu_section', $award->card_style ? route('awards.index', absolute: false) : $award->url());

        $award->load([
            'media', 'categories', 'facts', 'sources', 'letter.media', 'features.media',
            'editions' => fn ($q) => $q->with(['media', 'winners' => fn ($w) => $w->with(['media', 'category', 'facts', 'achiever'])]),
        ]);

        return view('site.awards.'.$award->kind, ['award' => $award]);
    }
}
