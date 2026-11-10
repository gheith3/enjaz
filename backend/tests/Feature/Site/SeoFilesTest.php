<?php

namespace Tests\Feature\Site;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoFilesTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_lists_routed_pages_but_not_hidden_ones(): void
    {
        Page::syncFromConfig();

        $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<loc>'.url('/').'</loc>', false);

        Page::query()->where('key', 'home')->update(['noindex' => true]);
        $this->get('/sitemap.xml')->assertDontSee('<loc>'.url('/').'</loc>', false);
    }

    public function test_robots_blocks_everything_outside_production(): void
    {
        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /');
    }
}
