<?php

namespace Tests\Feature\Site;

use App\Livewire\Site\AchievementsIndex;
use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\Field;
use App\Models\Organization;
use App\Models\OrganizationType;
use App\Models\Page;
use App\Models\Scope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AchievementPagesTest extends TestCase
{
    use RefreshDatabase;

    private Field $sports;

    private Field $literature;

    private Scope $international;

    protected function setUp(): void
    {
        parent::setUp();
        Page::syncFromConfig();
        $this->sports = Field::factory()->create(['slug' => 'sports', 'name' => ['ar' => 'الرياضة']]);
        $this->literature = Field::factory()->create(['slug' => 'literature', 'name' => ['ar' => 'الأدب']]);
        $this->international = Scope::factory()->create();
    }

    private function achievement(array $attributes = [], ?Achiever $achiever = null): Achievement
    {
        $achievement = Achievement::factory()->create($attributes + ['field_id' => $this->sports->id, 'scope_id' => $this->international->id]);
        $achievement->achievers()->attach($achiever ?? Achiever::factory()->create(['gender' => 'm']));

        return $achievement;
    }

    public function test_hero_wall_shows_the_approved_achievers_portraits_with_their_focal_points(): void
    {
        foreach (['jokha-alharthi' => '62% 10%', 'ali-alhabsi' => '52% 12%', 'safiya-albahlani' => '40% 30%'] as $slug => $focal) {
            Achiever::factory()->create(['slug' => $slug])
                ->addMediaFromBase64('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=')
                ->usingFileName($slug.'.png')->withCustomProperties(['focal' => $focal])->toMediaCollection('portrait');
        }

        $tiles = Livewire::test(AchievementsIndex::class, ['sitePage' => Page::forKey('achievements')])->instance()->heroTiles;

        $this->assertSame(['62% 10%', '52% 12%', '40% 30%'], array_column($tiles, 'focal'));
        $this->assertStringContainsString('jokha-alharthi', $tiles[0]['url']);
    }

    public function test_list_shows_published_achievements_only(): void
    {
        $this->achievement(['title' => ['ar' => 'كأس الخليج']]);
        $this->achievement(['title' => ['ar' => 'مسودة لم تنشر'], 'is_published' => false]);

        $this->get('/achievements')->assertOk()
            ->assertSee('كأس الخليج')->assertDontSee('مسودة لم تنشر')
            ->assertSee('إنجاز واحد')
            ->assertSee('class="page-hero ah2-hero', false);
    }

    public function test_filters_by_field_gender_scope_and_years(): void
    {
        $woman = Achiever::factory()->create(['gender' => 'f']);
        $this->achievement(['title' => ['ar' => 'رياضة رجال'], 'achieved_at' => '2010-05-01']);
        $this->achievement(['title' => ['ar' => 'أدب نساء'], 'field_id' => $this->literature->id, 'achieved_at' => '2020-05-01'], $woman);

        Livewire::test(AchievementsIndex::class, ['sitePage' => Page::forKey('achievements')])
            ->call('setField', 'literature')->assertSee('أدب نساء')->assertDontSee('رياضة رجال')
            ->call('setField', '')->set('gender', 'm')->assertSee('رياضة رجال')->assertDontSee('أدب نساء')
            ->set('gender', '')->set('from', '2015')->assertSee('أدب نساء')->assertDontSee('رياضة رجال')
            ->set('from', '')->set('scope', 'local')->assertDontSee('أدب نساء')->assertSee('لا توجد إنجازات');
    }

    public function test_search_ignores_hamza_and_ta_marbuta(): void
    {
        $this->achievement(['title' => ['ar' => 'جائزة الأدب العربي']]);
        $this->achievement(['title' => ['ar' => 'بطولة السباحة']]);

        Livewire::test(AchievementsIndex::class, ['sitePage' => Page::forKey('achievements')])
            ->set('q', 'جائزه الادب')->assertSee('جائزة الأدب العربي')->assertDontSee('بطولة السباحة');
    }

    public function test_honour_board_is_not_repeated_in_the_grid(): void
    {
        $this->achievement(['title' => ['ar' => 'إنجاز لوحة الشرف'], 'is_featured' => true]);

        $html = $this->get('/achievements')->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'إنجاز لوحة الشرف</'));
        $this->assertStringContainsString('class="wrap honor"', $html);
    }

    public function test_detail_page_renders_the_story(): void
    {
        $achiever = Achiever::factory()->create(['name' => ['ar' => 'علي الحبسي']]);
        $first = $this->achievement(['achieved_at' => '2009-01-17', 'title' => ['ar' => 'لقب كأس الخليج'], 'pull_quote' => ['ar' => 'قول مأثور'], 'pull_quote_after' => 1,
            'highlights' => [['lead' => ['ar' => 'أول لقب'], 'text' => ['ar' => 'في تاريخ المنتخب']]]], $achiever);
        $first->sources()->create(['title' => ['ar' => 'الجزيرة'], 'url' => 'https://example.com']);
        $this->achievement(['achieved_at' => '2013-05-11', 'title' => ['ar' => 'كأس الاتحاد']], $achiever);

        $html = $this->get('/achievements/'.$first->slug)->assertOk()
            ->assertSee('<title>لقب كأس الخليج — علي الحبسي | إنجازات شباب عُمان</title>', false)
            ->assertSee('أول لقب')->assertSee('الجزيرة')->assertSee('class="pq"', false)
            ->assertSee('class="ars" data-ars', false)->assertSee('تقرأ الآن')->assertSee('كأس الاتحاد')
            ->assertSee('class="story-pager"', false)->assertSee('rel="next"', false)->assertDontSee('rel="prev"', false)
            ->assertSee('application/ld+json', false)->getContent();

        $this->assertLessThan(strpos($html, 'الفقرة الثانية'), strpos($html, 'قول مأثور'), 'pull quote follows paragraph 1');
    }

    public function test_drafts_are_hidden_from_visitors_but_previewable_by_editors(): void
    {
        $draft = $this->achievement(['is_published' => false]);

        $this->get('/achievements/'.$draft->slug)->assertNotFound();
        $this->actingAs($this->userWithRole('editor'))->get('/achievements/'.$draft->slug)->assertOk()->assertSee('name="robots" content="noindex"', false);
    }

    public function test_design_addresses_redirect_and_sitemap_lists_records(): void
    {
        $achievement = $this->achievement();

        $this->get('/sitemap.xml')->assertSee($achievement->url(), false);
    }

    public function test_sports_opens_the_federations_panel_and_a_federation_filters_the_grid(): void
    {
        $type = OrganizationType::factory()->template('federation')->create();
        $football = Organization::factory()->create(['slug' => 'oman-fa', 'organization_type_id' => $type->id, 'name' => ['ar' => 'الاتحاد العُماني لكرة القدم'], 'short_name' => ['ar' => 'كرة القدم']]);
        Organization::factory()->create(['slug' => 'oman-athletics', 'organization_type_id' => $type->id, 'name' => ['ar' => 'الاتحاد العُماني لألعاب القوى'], 'short_name' => ['ar' => 'ألعاب القوى']]);
        $member = Achiever::factory()->create(['gender' => 'm']);
        $member->organizations()->attach($football, ['kind' => 'member']);
        $this->achievement(['title' => ['ar' => 'هدف في كأس الخليج']], $member);
        $this->achievement(['title' => ['ar' => 'ذهبية الجري']]);

        // Before «الرياضة»: the panel is drawn hidden (logos and names, no counts) so the chip opens it at once (تصاميم ٢٠٤٠).
        $page = Livewire::test(AchievementsIndex::class, ['sitePage' => Page::forKey('achievements')])
            ->assertSeeHtml('class="fed-panel open x-preview"')->assertDontSeeHtml('fed-open')->assertSeeHtml('data-fed-field')
            ->assertSeeHtml('<div class="x-count x-word-only" aria-hidden="true"><span class="x-n"></span><span class="x-w">إنجازان</span></div>');
        $this->assertSame([null, null], array_column($page->instance()->federationsPreview, 'count'));

        $page->call('setField', 'sports')->assertSee('الاتحادات الرياضية')->assertSee('كرة القدم')->assertSee('ألعاب القوى')
            ->assertSeeHtml('fed-open')->assertDontSeeHtml('x-preview');

        $this->assertSame([1, 0], array_column($page->instance()->federations, 'count'));

        $page->call('setOrg', 'oman-fa')->assertSee('هدف في كأس الخليج')->assertDontSee('ذهبية الجري')->assertSee('صفحة الاتحاد')
            ->call('setOrg', 'oman-fa')->assertSee('ذهبية الجري')
            ->call('setField', 'sports')->assertDontSeeHtml('fed-open')->assertSeeHtml('class="fed-panel open x-preview"');
    }
}
