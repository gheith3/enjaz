<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Contracts\View\View;

class AboutController extends Controller
{
    /** من نحن (about.html): its own header shell; the lists come from the page's sections and items. */
    public function __invoke(): View
    {
        return view('site.pages.about', ['page' => Page::forKey('about')]);
    }
}
