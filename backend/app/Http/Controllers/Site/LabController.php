<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\LabItem;
use App\Models\Page;
use Illuminate\Contracts\View\View;

class LabController extends Controller
{
    /** المختبر (science.html): hero, inventions showcase and publications table (desktop), tabs (phone). */
    public function index(): View
    {
        $items = LabItem::query()->listed()->with(['media', 'organization'])->get();

        return view('site.lab.index', [
            'page' => Page::forKey('lab'),
            'inventions' => $items->where('kind', 'invention')->values(),
            'publications' => $items->where('kind', 'publication')->values(),
        ]);
    }

    /** كل الابتكارات / كل المنشورات (lab-all.html). */
    public function list(string $segment): View
    {
        $kind = array_search($segment, LabItem::SEGMENTS, true);
        abort_if($kind === false, 404);
        request()->attributes->set('menu_section', route('lab.index', absolute: false));

        return view('site.lab.list', ['page' => Page::forKey('lab'), 'kind' => $kind]);
    }

    /** One invention or publication (lab-item.html). Drafts: preview for dashboard users only. */
    public function show(LabItem $item): View
    {
        abort_unless($item->isLive() || auth()->user()?->can('View:LabItem'), 404);
        request()->attributes->set('menu_section', route('lab.index', absolute: false));
        $item->load(['media', 'organization', 'facts', 'sources']);

        return view('site.lab.show', [
            'page' => Page::forKey('lab'),
            'item' => $item,
            'others' => LabItem::query()->listed()->ofKind($item->kind)->whereKeyNot($item->getKey())->with('media')->limit(4)->get(),
        ]);
    }
}
