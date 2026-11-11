<?php

namespace Tests\Feature\Import;

use App\Models\News;
use App\Models\Organization;
use App\Models\Redirect;

/** Design import: Organizations, their news and links. */
class ImportOrganizationsTest extends ImportTestCase
{
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
}
