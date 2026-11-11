<?php

namespace Tests\Feature\Import;

use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\LeaderQuote;
use App\Models\Page;
use App\Models\PageSection;

/** Design import: Inspiring people and the home page. */
class ImportHomeTest extends ImportTestCase
{
    public function test_it_imports_the_inspiring_people_and_the_home_page(): void
    {
        $this->artisan('injazat:import-design', ['--only' => ['achievements', 'organizations', 'awards', 'lab', 'inspiring', 'home']])->assertSuccessful();

        $this->assertSame(4, Achiever::query()->where('is_inspiring', true)->count());
        $this->assertSame('الفن', Achiever::query()->where('slug', 'safiya-albahlani')->first()->inspiring_field);
        $this->assertSame(4, PageSection::query()->where('key', 'inspiring.himam')->first()->items()->count());
        $this->assertSame(3, LeaderQuote::query()->count());
        $this->assertSame('feature', Achievement::query()->where('home_slot', 'feature')->first()?->home_slot);
        $this->assertSame(4, PageSection::query()->where('key', 'home.sports')->first()->items()->count());
        $this->assertSame(['gov', 'private', 'military', 'sports'], PageSection::query()->where('key', 'home.partners')->first()->setting('row_order'));
        $this->get('/')->assertOk()->assertSee('إن الشباب هم ثروة الأمم');
        $this->get('/inspiring')->assertOk()->assertSee('زينب الحراصي');
    }
}
