<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Contracts\View\View;

class ContactController extends Controller
{
    /** تواصل معنا (contact.html): channels, the Livewire form and the place (map image = the page's «cutout»). */
    public function __invoke(): View
    {
        return view('site.pages.contact', ['page' => Page::forKey('contact')]);
    }
}
