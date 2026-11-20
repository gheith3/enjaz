<?php

namespace Tests\Feature\Site;

use App\Livewire\Site\AchieversIndex;
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

    public function test_cards_show_the_first_name_and_the_tribe(): void
    {
        $short = fn (string $name) => (new Achiever(['name' => ['ar' => $name]]))->shortName();

        $this->assertSame('علي الحبسي', $short('علي بن عبدالله الحبسي'));
        $this->assertSame('د. جوخة الحارثية', $short('د. جوخة الحارثية'));
        $this->assertSame('الدكتور حسام العريمي', $short('الدكتور حسام بن سعيد العريمي'));
        $this->assertSame('عبد الله الكندي', $short('عبد الله بن سالم الكندي'));
        $this->assertSame('فيصل آل سعيد', $short('فيصل بن علي آل سعيد'));
        $this->assertSame('محمد المالكي', $short('محمد عامر المالكي'));

        Achiever::factory()->create(['name' => ['ar' => 'سالم بن أحمد الحبسي']]);
        $this->get('/achievers')->assertSee('>سالم الحبسي<', false);
    }

    public function test_sports_opens_the_federations_panel_and_a_federation_filters_the_achievers(): void
    {
        $sports = Field::factory()->create(['slug' => 'sports', 'name' => ['ar' => 'الرياضة']]);
        $type = OrganizationType::factory()->template('federation')->create();
        $football = Organization::factory()->create(['slug' => 'oman-fa', 'organization_type_id' => $type->id, 'name' => ['ar' => 'الاتحاد العُماني لكرة القدم'], 'short_name' => ['ar' => 'كرة القدم']]);
        Achiever::factory()->create(['name' => ['ar' => 'حارس المنتخب'], 'field_id' => $sports->id])->organizations()->attach($football, ['kind' => 'member']);
        Achiever::factory()->create(['name' => ['ar' => 'عداء الماراثون'], 'field_id' => $sports->id]);

        Livewire::test(AchieversIndex::class, ['sitePage' => Page::forKey('achievers')])
            ->call('setField', 'sports')->assertSee('الاتحادات الرياضية')->assertSee('كرة القدم')
            ->call('setOrg', 'oman-fa')->assertSee('حارس المنتخب')->assertDontSee('عداء الماراثون')->assertSee('منجزون بالشراكة مع')
            ->call('setOrg', '')->assertSee('عداء الماراثون');
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
            ->assertSee('قصة البداية')
            ->assertSee('<dialog class="library-dialog" id="library-videos"', false)
            ->assertSee('<dialog class="library-dialog" id="library-photos"', false);
    }

    public function test_profile_prints_the_achievements_data_of_design_9(): void
    {
        $international = Scope::factory()->create();
        $local = Scope::factory()->local()->create();
        $achiever = Achiever::factory()->create();
        $booker = Achievement::factory()->create([
            'slug' => 'man-booker', 'scope_id' => $international->id, 'achieved_at' => '2019-05-21', 'date_precision' => 'day',
            'rank' => 'award', 'issuer' => ['ar' => 'جائزة مان بوكر الدولية'], 'project' => ['ar' => 'سيدات القمر'],
            'stat' => ['ar' => 'أول كاتبة عربية تفوز بها'], 'short_title' => ['ar' => 'مان بوكر'], 'is_profile_highlight' => true,
            'country' => ['ar' => 'المملكة المتحدة'], 'body' => ['ar' => '<p>الفقرة الأولى</p><ul><li>نقطة</li></ul>'],
            'highlights' => [['lead' => ['ar' => 'أول كاتبة عربية'], 'text' => ['ar' => 'تفوز بالجائزة']]],
        ]);
        $booker->sources()->create(['title' => ['ar' => 'مؤسسة بوكر'], 'subtitle' => ['ar' => 'إعلان الفوز'], 'url' => 'https://example.com/booker']);
        $honor = Achievement::factory()->create(['slug' => 'local-honor', 'scope_id' => $local->id, 'achieved_at' => '2016-01-01', 'date_precision' => 'year', 'rank' => null, 'country' => null]);
        $achiever->achievements()->attach([$booker->id, $honor->id]);

        $html = $this->get('/achievers/'.$achiever->slug)->assertOk()
            ->assertSee('class="kc" data-kc', false)->assertSee('<div class="aj" data-aj></div>', false)
            ->assertDontSee('data-ach-grid', false)
            ->getContent();

        preg_match('#<script type="application/json" id="aj-data">(.*?)</script>#s', $html, $match);
        $items = collect(json_decode($match[1], true)['items'])->keyBy('slug');

        $this->assertEquals([
            'rank' => 'award', 'org' => 'جائزة مان بوكر الدولية', 'project' => 'سيدات القمر', 'stat' => 'أول كاتبة عربية تفوز بها',
            'short' => 'مان بوكر', 'featured' => 1, 'scope' => 'international', 'iso' => '2019-05-21', 'country' => 'المملكة المتحدة',
            'details' => "الفقرة الأولى\n\n• نقطة", 'highlights' => ['<b>أول كاتبة عربية</b> تفوز بالجائزة'], 'bold' => ['أول كاتبة عربية'],
            'sources' => [['org' => 'مؤسسة بوكر', 'title' => 'إعلان الفوز', 'url' => 'https://example.com/booker']],
        ], collect($items['man-booker'])->only(['rank', 'org', 'project', 'stat', 'short', 'featured', 'scope', 'iso', 'country', 'details', 'highlights', 'bold', 'sources'])->all());
        $this->assertEquals(['rank' => 'award', 'featured' => 0, 'scope' => 'local', 'iso' => '2016', 'country' => '—'],
            collect($items['local-honor'])->only(['rank', 'featured', 'scope', 'iso', 'country'])->all());
    }

    public function test_draft_profile_is_hidden(): void
    {
        $this->get('/achievers/'.Achiever::factory()->draft()->create()->slug)->assertNotFound();
    }
}
