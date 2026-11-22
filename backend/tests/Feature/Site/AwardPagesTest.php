<?php

namespace Tests\Feature\Site;

use App\Models\Achiever;
use App\Models\Award;
use App\Models\AwardEdition;
use App\Models\Page;
use App\Support\Awards\BilarabContent;
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
            ->assertSee('<li>أحمد</li><li>أميمة</li>', false)->assertSee('جناح إكسبو')->assertSee('data-to="472"', false)
            ->assertDontSee('data-jx', false);

        $draft = Award::factory()->create(['is_published' => false]);
        $this->get($draft->url())->assertNotFound();
        $this->assertInstanceOf(AwardEdition::class, $edition);
    }

    public function test_architecture_award_page_shows_the_2050_design(): void
    {
        $award = Award::factory()->kind('architecture')->create(['slug' => 'bilarab', 'firsts' => ['ar' => 'دروع رقمية'], 'extra' => ['firsts_label' => ['ar' => 'سابقة عربية']]]);
        BilarabContent::apply($award);
        $edition = $award->editions()->create(['number' => 1, 'year' => '2022', 'title' => ['ar' => 'معلم مطرح'], 'map_label' => ['ar' => 'مطرح'],
            'entries_count' => 356, 'finals_count' => 10, 'jury' => ['ar' => 'م. فؤاد الكندي']]);
        $edition->winners()->create(['rank' => 1, 'work' => ['ar' => 'ميدان مطرح'], 'why' => ['ar' => 'جسر أيقوني']]);
        foreach (['الموضوع', 'المشاركات', 'الفرز', 'التتويج', 'التنفيذ'] as $i => $step) {
            $award->facts()->create(['group' => 'path', 'label' => ['ar' => $step], 'value' => ['ar' => 'شرح '.$step], 'sort_order' => $i + 1]);
        }
        $award->facts()->create(['group' => 'who', 'label' => ['ar' => 'الجنسية'], 'value' => ['ar' => 'عُماني أو عُمانية'], 'icon' => 'flag']);
        $award->features()->create(['key' => 'ihya', 'chip' => ['ar' => '19–22 فبراير 2025'], 'title' => ['ar' => 'مبادرة إحياء العمارة'], 'subtitle' => ['ar' => 'متحف عُمان عبر الزمان – منح']]);

        $this->get($award->url())->assertOk()
            ->assertSee('<em>إلى معالم حقيقية</em>', false)->assertSee('تمكين المواهب')
            ->assertSee('data-jx', false)->assertSee('data-chip="مطرح ٢٠٢٢"', false)->assertSee('data-chip="٣٥٦ مشاركة"', false)->assertSee('data-chip="سابقة عربية"', false)
            ->assertSee('هل تنطبق عليك الشروط؟')->assertSee('عُماني أو عُمانية')->assertSee('الحد الأقصى للعمر')
            ->assertSee('class="cz-band c1"', false)->assertSee('م. فؤاد الكندي')->assertSee('جسر أيقوني')
            ->assertSee('ركن الجائزة')->assertSee('<i>فبراير</i><b>١٩–٢٢</b><em>٢٠٢٥</em>', false)->assertSee('متحف عُمان عبر الزمان');
    }

    public function test_bilarab_2050_content_is_added_once_and_keeps_the_editors_changes(): void
    {
        $award = Award::factory()->kind('architecture')->create(['slug' => 'bilarab', 'extra' => ['who_big' => ['ar' => '٤٠']]]);
        $award->facts()->create(['group' => 'goals', 'label' => ['ar' => 'هدف المحرر'], 'value' => ['ar' => 'نص']]);

        BilarabContent::apply($award);
        BilarabContent::apply($award->refresh());

        $this->assertSame('٤٠', $award->refresh()->label('who_big'));
        $this->assertSame('الحد الأقصى للعمر', $award->label('who_big_text'));
        $this->assertSame(['هدف المحرر'], $award->factsIn('goals')->pluck('label')->all());
        $this->assertCount(4, $award->factsIn('initiative'));
        $this->assertCount(4, $award->factsIn('expo'));
    }
}
