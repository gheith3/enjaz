<?php

namespace Tests\Feature\Import;

use App\Models\Award;
use App\Models\Redirect;

/** Design import: Awards, editions and winners. */
class ImportAwardsTest extends ImportTestCase
{
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
}
