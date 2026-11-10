<?php

namespace Tests\Feature\Site;

use App\Models\Achiever;
use App\Models\Award;
use App\Models\AwardEdition;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AwardPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Page::syncFromConfig();
    }

    public function test_awards_list_shows_the_awards_with_a_card(): void
    {
        Award::factory()->create(['name' => ['ar' => 'جائزة الثقافة'], 'card_style' => 'culture', 'card_text' => ['ar' => 'تتويجات']]);
        Award::factory()->kind('architecture')->create(['name' => ['ar' => 'جائزة العمارة']]);
        Award::factory()->create(['name' => ['ar' => 'جائزة مسودة'], 'card_style' => 'vol', 'is_published' => false]);

        $this->get('/awards')->assertOk()->assertSee('جائزة الثقافة')->assertSee('تتويجات')
            ->assertDontSee('جائزة العمارة')->assertDontSee('جائزة مسودة');
    }

    public function test_culture_award_page_shows_branches_medals_and_current_edition(): void
    {
        $award = Award::factory()->create(['slug' => 'culture', 'about' => ['ar' => 'جائزة سنوية']]);
        $lit = $award->categories()->create(['kind' => 'branch', 'key' => 'lit', 'name' => ['ar' => 'الآداب']]);
        $award->categories()->create(['kind' => 'prize', 'key' => 'oman', 'name' => ['ar' => 'الدورة العُمانية'], 'medal' => ['ar' => 'وسام الاستحقاق']]);
        $edition = $award->editions()->create(['number' => 5, 'year' => '2016', 'type' => 'oman', 'sort_order' => 1]);
        $jokha = Achiever::factory()->create(['name' => ['ar' => 'جوخة']]);
        $edition->winners()->create(['award_category_id' => $lit->id, 'name' => ['ar' => 'جوخة الحارثية'], 'field' => ['ar' => 'الرواية'], 'achiever_id' => $jokha->id]);
        $award->editions()->create(['number' => 6, 'year' => '2026', 'type' => 'oman', 'is_current' => true, 'status' => ['ar' => 'يُرتقب الإعلان'],
            'open_fields' => [['category' => 'lit', 'text' => ['ar' => 'القصة القصيرة']]], 'sort_order' => 2]);

        $this->get($award->url())->assertOk()->assertSee('جائزة سنوية')->assertSee('الآداب')->assertSee('جوخة الحارثية')
            ->assertSee('الدورة الخامسة')->assertSee('وسام الاستحقاق')->assertSee('القصة القصيرة')->assertSee('يُرتقب الإعلان')
            ->assertSee($jokha->url(), false);
    }

    public function test_volunteer_award_page_shows_podiums_and_early_editions(): void
    {
        $award = Award::factory()->kind('volunteer')->create(['slug' => 'volunteer']);
        $ind = $award->categories()->create(['kind' => 'category', 'key' => 'ind', 'name' => ['ar' => 'الأفراد']]);
        $award->editions()->create(['number' => 1, 'year' => '2011', 'entries_count' => 101, 'sort_order' => 1]);
        $edition = $award->editions()->create(['number' => 2, 'year' => '2019', 'entries_count' => 112, 'sort_order' => 2]);
        $edition->winners()->create(['award_category_id' => $ind->id, 'rank' => 1, 'name' => ['ar' => 'سعيد المقيمي'], 'work' => ['ar' => 'شق طرق جبلية']]);

        $this->get($award->url())->assertOk()->assertSee('سعيد المقيمي')->assertSee('شق طرق جبلية')->assertSee('١١٢ مشروعًا')
            ->assertSee('الفائزون قيد التوثيق');
    }

    public function test_architecture_award_page_and_drafts(): void
    {
        $award = Award::factory()->kind('architecture')->create(['slug' => 'bilarab', 'lead' => ['ar' => 'مسابقة **برؤية عالمية**']]);
        $edition = $award->editions()->create(['number' => 1, 'year' => '2022', 'title' => ['ar' => 'معلم مطرح'], 'entries_count' => 356, 'finals_count' => 10]);
        $edition->winners()->create(['rank' => 1, 'work' => ['ar' => 'ميدان مطرح'], 'team' => [['ar' => 'أحمد'], ['ar' => 'أميمة']]]);
        $award->features()->create(['title' => ['ar' => 'جناح إكسبو'], 'entries_count' => 116]);

        $this->get($award->url())->assertOk()->assertSee('<b>برؤية عالمية</b>', false)->assertSee('معلم مطرح')->assertSee('ميدان مطرح')
            ->assertSee('أحمد · أميمة')->assertSee('جناح إكسبو')->assertSee('data-to="472"', false);

        $draft = Award::factory()->create(['is_published' => false]);
        $this->get($draft->url())->assertNotFound();
        $this->assertInstanceOf(AwardEdition::class, $edition);
    }
}
