<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Response;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

/**
 * Published pages and records. Each content phase adds its models here.
 */
class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $sitemap = Sitemap::create();

        Page::query()->where('noindex', false)->get()
            ->each(function (Page $page) use ($sitemap) {
                if ($url = $page->publicUrl()) {
                    $sitemap->add(Url::create($url)->setLastModificationDate($page->updated_at));
                }
            });

        return response($sitemap->render(), 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
