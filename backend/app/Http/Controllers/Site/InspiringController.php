<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Achiever;
use App\Models\Page;
use Illuminate\Contracts\View\View;

class InspiringController extends Controller
{
    /** ملهمون من عُمان (inspiring.html): hero panels and photo wall, the wall with field chips, sections (همم بلا حدود), nominate. */
    public function __invoke(): View
    {
        return view('site.pages.inspiring', [
            'page' => Page::forKey('inspiring'),
            'people' => Achiever::query()->published()->where('is_inspiring', true)->with(['media', 'field'])->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }
}
