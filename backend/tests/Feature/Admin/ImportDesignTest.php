<?php

namespace Tests\Feature\Admin;

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Redirect;
use App\Models\SocialLink;
use App\Settings\GeneralSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Runs the real importer against the repo's design folder (files go to the fake media disk).
 */
class ImportDesignTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake(config('media-library.disk_name'));
    }

    public function test_it_imports_the_site_structure_from_the_design(): void
    {
        $this->artisan('injazat:import-design')->assertSuccessful();

        $this->assertSame(12, MenuItem::query()->where('location', 'header')->count());
        $this->assertSame('جائزة بلعرب', MenuItem::query()->where('url', '/awards/bilarab')->where('location', 'header')->first()->label);
        $this->assertSame('المنجزون', MenuItem::query()->where('url', '/achievers')->where('location', 'header')->first()->label_short);
        $this->assertGreaterThan(0, MenuItem::query()->where('location', 'footer_2')->count());

        $settings = app(GeneralSettings::class);
        $this->assertNotEmpty($settings->phones);
        $this->assertNotNull($settings->email);
        Storage::disk(config('media-library.disk_name'))->assertExists($settings->logo);
        $this->assertStringStartsWith('media/', $settings->logo);

        $this->assertSame(6, SocialLink::query()->whereNull('linkable_type')->count());
        $this->assertSame('/achievers', Redirect::query()->where('from_path', '/achievers.html')->value('to_path'));
        $this->assertNotEmpty(Page::query()->where('key', 'about')->first()->meta_description);
    }

    public function test_running_it_again_keeps_editors_changes(): void
    {
        $this->artisan('injazat:import-design')->assertSuccessful();
        MenuItem::query()->where('url', '/contact')->where('location', 'header')->first()
            ->setTranslation('label', 'ar', 'راسلنا')->save();

        $this->artisan('injazat:import-design')->assertSuccessful();

        $this->assertSame(12, MenuItem::query()->where('location', 'header')->count());
        $this->assertTrue(MenuItem::query()->where('location', 'header')->get()->contains('label', 'راسلنا'));
    }
}
