<?php

namespace Tests\Feature\Site;

use App\Livewire\Site\AchieversIndex;
use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\Field;
use App\Models\Page;
use App\Models\Scope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AchieverPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Page::syncFromConfig();
    }

    public function test_list_shows_published_achievers_and_the_week_pair(): void
    {
        $field = Field::factory()->create(['name' => ['ar' => 'الرياضة']]);
        Achiever::factory()->create(['name' => ['ar' => 'علي الحبسي'], 'gender' => 'm', 'field_id' => $field->id, 'week_from' => now()->subDay(), 'week_teaser' => ['ar' => 'من مرمى المنتخب']]);
        Achiever::factory()->create(['name' => ['ar' => 'جوخة الحارثي'], 'gender' => 'f', 'week_from' => now()->subDay()]);
        Achiever::factory()->draft()->create(['name' => ['ar' => 'منجز مسودة']]);

        $this->get('/achievers')->assertOk()
            ->assertSee('علي الحبسي')->assertSee('جوخة الحارثي')->assertDontSee('منجز مسودة')
            ->assertSee('منجز الأسبوع')->assertSee('منجزة الأسبوع')->assertSee('من مرمى المنتخب')
            ->assertSee('منجزان');
    }

    public function test_gender_toggle_and_search(): void
    {
        Achiever::factory()->create(['name' => ['ar' => 'أحمد'], 'gender' => 'm']);
        Achiever::factory()->create(['name' => ['ar' => 'عائشة السيفي'], 'gender' => 'f']);

        Livewire::test(AchieversIndex::class, ['sitePage' => Page::forKey('achievers')])
            ->call('toggleGender', 'f')->assertSee('عائشة السيفي')->assertDontSee('>أحمد<', false)
            ->call('toggleGender', 'f')->assertSee('أحمد')
            ->set('q', 'عايشه')->assertSee('عائشة السيفي')->assertDontSee('>أحمد<', false);
    }

    public function test_profile_shows_counts_journey_quotes_and_id_card(): void
    {
        $international = Scope::factory()->create();
        $local = Scope::factory()->local()->create();
        $achiever = Achiever::factory()->create(['name' => ['ar' => 'علي الحبسي', 'en' => 'Ali Al-Habsi'], 'story' => ['ar' => 'قصة البداية'], 'quotes_note' => ['ar' => 'الأقوال مترجمة'], 'short_bio' => ['ar' => 'حارس مرمى']]);
        $achiever->achievements()->attach(Achievement::factory()->count(2)->create(['scope_id' => $international->id]));
        $achiever->achievements()->attach(Achievement::factory()->create(['scope_id' => $local->id]));
        $achiever->journeyItems()->create(['year' => '2009', 'title' => ['ar' => 'كأس الخليج'], 'icon' => 'trophy']);
        $achiever->quotes()->create(['text' => ['ar' => 'لاعب رائع'], 'name' => ['ar' => 'مدرب']]);

        $this->get('/achievers/'.$achiever->slug)->assertOk()
            ->assertSee('<p class="en" lang="en">Ali Al-Habsi</p>', false)
            ->assertSee('data-total="3" data-intl="2" data-local="1"', false)
            ->assertSee('data-icon="trophy"', false)->assertSee('لاعب رائع')->assertSee('الأقوال مترجمة')
            ->assertSee('id="member-card-data"', false)->assertSee('"bio":"حارس مرمى"', false)
            ->assertSee('قصة البداية');
    }

    public function test_draft_profile_is_hidden(): void
    {
        $this->get('/achievers/'.Achiever::factory()->draft()->create()->slug)->assertNotFound();
    }
}
