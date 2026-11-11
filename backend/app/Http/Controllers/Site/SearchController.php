<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class SearchController extends Controller
{
    public function __invoke(): View
    {
        return view('site.pages.search');
    }
}
