<?php

namespace Tests\Feature\Import;

use App\Models\Page;
use App\Models\PageSection;

/** Design import: The about page. */
class ImportAboutTest extends ImportTestCase
{
    public function test_it_imports_the_about_page(): void
    {
        $this->artisan('injazat:import-design', ['--only' => ['about']])->assertSuccessful();

        $count = fn (string $key) => PageSection::query()->where('key', $key)->first()->items()->count();
        $this->assertSame(9, $count('about.fields'));
        $this->assertSame(17, $count('about.journey'));
        $this->assertSame(1, $count('about.founder'));
        $this->assertSame(9, $count('about.testimonials'));
        $this->assertSame(5, $count('about.team'));
        $page = Page::forKey('about');
        $this->assertSame('الإلهام', $page->label('value1_tag'));
        $this->assertTrue($page->hasMedia('cutout'));
        $this->get('/about')->assertOk()->assertSee('منذر بن إبراهيم المعمري');
    }
}
