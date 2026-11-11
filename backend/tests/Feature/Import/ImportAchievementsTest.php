<?php

namespace Tests\Feature\Import;

use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\Redirect;

/** Design import: Achievers and achievements. */
class ImportAchievementsTest extends ImportTestCase
{
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
}
