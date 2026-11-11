<?php

namespace Tests\Feature\Admin;

use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\Award;
use App\Models\LabItem;
use App\Models\MenuItem;
use App\Models\News;
use App\Models\Organization;
use App\Models\Page;
use App\Models\Redirect;
use App\Models\SocialLink;
use App\Settings\GeneralSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
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
        Queue::fake(); // image conversions are queued jobs: not needed here
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

    public function test_it_imports_achievers_and_achievements(): void
    {
        $this->artisan('injazat:import-design', ['--only' => ['achievements']])->assertSuccessful();

        $this->assertSame(6, Achiever::count());                     // sample duplicates skipped
        $ali = Achiever::query()->where('slug', 'ali-alhabsi')->firstOrFail();
        $this->assertSame('Ali Al-Habsi', $ali->getTranslation('name', 'en'));
        $this->assertSame(['total' => 11, 'international' => 6, 'local' => 5], $ali->medalStats()); // as the design's profile
        $this->assertSame(10, $ali->journeyItems()->count());
        $this->assertSame('ali-hero-cutout.png', $ali->getFirstMedia('cutout')->file_name);   // profile hero
        $this->assertSame('ali-trophies-cutout.png', $ali->getFirstMedia('week')->file_name); // week card

        $booker = Achievement::query()->where('slug', 'man-booker-2019')->firstOrFail();
        $this->assertSame('٢١ مايو ٢٠١٩', $booker->dateLabel());
        $this->assertCount(4, $booker->sources);
        $this->assertCount(2, $booker->highlightItems());
        $this->assertNotEmpty($booker->pull_quote);
        $this->assertSame('/achievements/aly-alhbsy-01', Redirect::query()->where('from_path', '/achievement-aly-alhbsy-01.html')->value('to_path'));
        $this->assertTrue(Achievement::query()->where('is_featured', true)->exists());
    }

    public function test_it_imports_organizations_and_links(): void
    {
        $this->artisan('injazat:import-design', ['--only' => ['achievements', 'organizations']])->assertSuccessful();

        $this->assertSame(53, Organization::count());   // 28 universities, 15 federations, 10 others
        $squ = Organization::query()->where('slug', 'squ')->firstOrFail();
        $this->assertSame('university', $squ->template());
        $this->assertSame(6, $squ->leaders()->count());
        $this->assertNotNull($squ->letter);
        $this->assertSame('staff', $squ->achievers()->where('slug', 'jokha-alharthi')->first()->pivot->kind);
        $this->assertSame(8, $squ->allAchievements()->count());
        $this->assertSame(0, News::query()->published()->count());   // sample news -> drafts
        $this->assertSame('/organizations/oman-fa', Redirect::query()->where('from_path', '/federation.html?org=oman-fa')->value('to_path'));
    }

    public function test_it_imports_awards_editions_and_winners(): void
    {
        $this->artisan('injazat:import-design', ['--only' => ['achievements', 'awards']])->assertSuccessful();

        $culture = Award::query()->where('slug', 'culture')->firstOrFail();
        $this->assertSame(18, $culture->winners()->count());
        $this->assertSame(1, $culture->editions()->where('is_current', true)->count());
        $this->assertSame(3, $culture->categories()->where('kind', 'branch')->count());
        $this->assertSame('jokha-alharthi', $culture->winners()->whereNotNull('achiever_id')->get()->first(fn ($w) => $w->achiever->slug === 'jokha-alharthi')?->achiever->slug);
        $this->assertSame(13, Award::query()->where('slug', 'volunteer')->first()->winners()->count());
        $bilarab = Award::query()->where('slug', 'bilarab')->first();
        $this->assertSame(7, $bilarab->winners()->count());
        $this->assertSame(2, $bilarab->features()->count());
        $this->assertSame('/awards/culture', Redirect::query()->where('from_path', '/award.html')->value('to_path'));
    }

    public function test_it_imports_the_lab(): void
    {
        $this->artisan('injazat:import-design', ['--only' => ['organizations', 'lab']])->assertSuccessful();

        $this->assertSame(5, LabItem::query()->where('kind', 'invention')->count());
        $this->assertSame(4, LabItem::query()->where('kind', 'publication')->count());
        $sense = LabItem::query()->where('slug', 'senseglass')->firstOrFail();
        $this->assertSame('جامعة صحار', $sense->organization?->name);
        $this->assertSame('ITEX ماليزيا', $sense->event);
        $this->assertSame('2026-05-01', $sense->happened_at->toDateString());
        $this->assertSame(4, $sense->facts()->count());
        $this->assertStringContainsString('<figure>', LabItem::query()->where('slug', 'etienne')->first()->body);
        $this->assertTrue(LabItem::query()->where('slug', 'carbon-ai')->first()->cover_is_illustrative);
        $lab = Page::query()->where('key', 'lab')->first();
        $this->assertSame('62', $lab->label('stat_inventions'));
        $this->assertSame(3, $lab->getMedia('hero')->count());
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
