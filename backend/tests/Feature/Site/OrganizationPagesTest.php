<?php

namespace Tests\Feature\Site;

use App\Livewire\Site\OrganizationAchievements;
use App\Livewire\Site\OrganizationPeople;
use App\Livewire\Site\OrganizationsIndex;
use App\Livewire\Site\UniversitiesIndex;
use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\News;
use App\Models\Organization;
use App\Models\OrganizationType;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrganizationPagesTest extends TestCase
{
    use RefreshDatabase;

    private OrganizationType $gov;

    private OrganizationType $academic;

    protected function setUp(): void
    {
        parent::setUp();
        Page::syncFromConfig();
        $this->gov = OrganizationType::factory()->template('institution')->create(['slug' => 'gov', 'name' => ['ar' => 'حكومي'], 'chip_label' => ['ar' => 'حكومية'], 'css_class' => 't-gov']);
        $this->academic = OrganizationType::factory()->template('university')->create(['slug' => 'academic', 'css_class' => 't-edu']);
    }

    public function test_organizations_list_groups_by_type_with_counters(): void
    {
        Organization::factory()->create(['organization_type_id' => $this->gov->id, 'name' => ['ar' => 'وزارة التراث']]);
        Organization::factory()->create(['organization_type_id' => $this->gov->id, 'name' => ['ar' => 'وزارة مسودة'], 'is_published' => false]);
        Organization::factory()->create(['organization_type_id' => $this->gov->id, 'name' => ['ar' => 'وزارة الثقافة'], 'is_featured' => true, 'tagline' => ['ar' => 'ترعى القطاعات']]);

        $this->get('/organizations')->assertOk()
            ->assertSee('وزارة التراث')->assertDontSee('وزارة مسودة')
            ->assertSee('data-count="2"', false)->assertSee('ترعى القطاعات')->assertSee('class="orgs-group g-gov"', false);
    }

    public function test_organizations_search_and_type_filter(): void
    {
        Organization::factory()->create(['organization_type_id' => $this->gov->id, 'name' => ['ar' => 'وزارة التراث']]);
        Organization::factory()->create(['organization_type_id' => $this->academic->id, 'name' => ['ar' => 'جامعة نزوى']]);

        Livewire::test(OrganizationsIndex::class, ['sitePage' => Page::forKey('organizations')])
            ->set('q', 'التراث')->assertSee('وزارة التراث')->assertDontSee('جامعة نزوى')
            ->set('q', '')->call('setType', 'academic')->assertSee('جامعة نزوى')->assertDontSee('وزارة التراث');
    }

    public function test_universities_list_with_spotlight_and_linked_counts(): void
    {
        $squ = Organization::factory()->create(['organization_type_id' => $this->academic->id, 'academic_group' => 'uni', 'name' => ['ar' => 'جامعة السلطان قابوس'], 'about' => ['ar' => 'نبذة'], 'sort_order' => 1]);
        Organization::factory()->create(['organization_type_id' => $this->academic->id, 'academic_group' => 'college', 'name' => ['ar' => 'كلية مسقط'], 'city' => ['ar' => 'مسقط']]);
        $staff = Achiever::factory()->create();
        $squ->achievers()->attach($staff, ['kind' => 'staff']);
        Achievement::factory()->count(2)->create()->each(fn ($a) => $a->achievers()->attach($staff));

        $this->get('/universities')->assertOk()->assertSee('class="u-spot"', false)->assertSee('منتسب منجز')->assertSee('إنجازان لمنتسبيها');

        Livewire::test(UniversitiesIndex::class, ['sitePage' => Page::forKey('universities')])
            ->call('setGroup', 'college')->assertSee('كلية مسقط')->assertDontSee('u-spot')
            ->call('setGroup', '')->set('q', 'مسقط')->assertSee('كلية مسقط')->assertDontSee('جامعة السلطان قابوس');
    }

    public function test_institution_page_shows_news_letter_and_rail(): void
    {
        $ministry = Organization::factory()->create(['organization_type_id' => $this->gov->id, 'name' => ['ar' => 'وزارة الثقافة'], 'focus_areas' => [['ar' => 'الشباب']], 'about' => ['ar' => 'نبذة الوزارة']]);
        Organization::factory()->create(['organization_type_id' => $this->gov->id, 'name' => ['ar' => 'وزارة أخرى']]);
        News::factory()->create(['organization_id' => $ministry->id, 'title' => ['ar' => 'خبر منشور'], 'happened_at' => '2024-05-23']);
        News::factory()->create(['organization_id' => $ministry->id, 'title' => ['ar' => 'خبر مسودة'], 'is_published' => false]);
        $ministry->letter()->create(['name' => ['ar' => 'الوزير'], 'role' => ['ar' => 'وزير'], 'body' => ['ar' => "فقرة أولى\n\nفقرة ثانية"], 'is_published' => true]);

        $this->get($ministry->url())->assertOk()
            ->assertSee('الشباب')->assertSee('نبذة الوزارة')->assertSee('خبر منشور')->assertDontSee('خبر مسودة')
            ->assertSee('٢٣ مايو')->assertSee('فقرة ثانية')->assertSee('وزارة أخرى');
        $this->get(route('organizations.achievements', $ministry))->assertOk()->assertSee('خبر منشور');
    }

    public function test_news_page_and_draft_visibility(): void
    {
        $org = Organization::factory()->create(['organization_type_id' => $this->gov->id, 'name' => ['ar' => 'وزارة الثقافة']]);
        $news = News::factory()->create(['organization_id' => $org->id, 'title' => ['ar' => 'مذكرة تفاهم'], 'facts' => [['label' => ['ar' => 'التصنيف'], 'value' => ['ar' => 'إنجاز']]]]);
        $draft = News::factory()->create(['organization_id' => $org->id, 'is_published' => false]);

        $this->get($news->url())->assertOk()->assertSee('مذكرة تفاهم')->assertSee('صادر عن')->assertSee('التصنيف');
        $this->get($draft->url())->assertNotFound();
        $this->get('/organizations/'.Organization::factory()->create(['organization_type_id' => $this->gov->id, 'is_published' => false])->slug)->assertNotFound();
    }

    public function test_university_page_and_its_achievements_and_people_pages(): void
    {
        $squ = Organization::factory()->create(['organization_type_id' => $this->academic->id, 'academic_group' => 'uni', 'name' => ['ar' => 'جامعة السلطان قابوس'],
            'about' => ['ar' => 'أول جامعة'], 'rankings' => [['year' => 2025, 'rank' => 362], ['year' => 2026, 'rank' => 334]]]);
        $squ->leaders()->create(['kind' => 'president', 'name' => ['ar' => 'الرئيس المؤسس'], 'from_year' => 1986, 'to_year' => 1997, 'sort_order' => 1]);
        $squ->leaders()->create(['kind' => 'president', 'name' => ['ar' => 'الرئيس الحالي'], 'from_year' => 2020, 'is_current' => true, 'sort_order' => 2]);
        $squ->units()->create(['kind' => 'college', 'year' => 1986, 'name' => ['ar' => 'كلية الهندسة']]);
        $staff = Achiever::factory()->create(['name' => ['ar' => 'د. جوخة'], 'gender' => 'f']);
        $student = Achiever::factory()->create(['name' => ['ar' => 'طالب منجز'], 'gender' => 'm']);
        $squ->achievers()->attach($staff, ['kind' => 'staff']);
        $squ->achievers()->attach($student, ['kind' => 'student']);
        Achievement::factory()->create(['title' => ['ar' => 'جائزة الأدب']])->achievers()->attach($staff);
        Achievement::factory()->create(['title' => ['ar' => 'ميدالية الطالب']])->achievers()->attach($student);
        Achievement::factory()->create(['title' => ['ar' => 'اعتماد الجامعة']])->organizations()->attach($squ);

        $this->get($squ->url())->assertOk()->assertSee('أول جامعة')->assertSee('الرئيس المؤسس')->assertSee('كلية الهندسة')
            ->assertSee('▲ 28')->assertSee('جائزة الأدب')->assertSee('د. جوخة')->assertSee(route('organizations.people', $squ));
        $this->get(route('organizations.achievements', $squ))->assertOk()->assertSee('اعتماد الجامعة');
        $this->get(route('organizations.people', $squ))->assertOk()->assertSee('طالب منجز');

        Livewire::test(OrganizationAchievements::class, ['organization' => $squ])
            ->call('setType', 'staff')->assertSee('جائزة الأدب')->assertDontSee('ميدالية الطالب')
            ->call('setType', '')->call('toggleGender', 'm')->assertSee('ميدالية الطالب')->assertDontSee('جائزة الأدب');
        Livewire::test(OrganizationPeople::class, ['organization' => $squ])
            ->call('setKind', 'student')->assertSee('طالب منجز')->assertDontSee('د. جوخة');
    }

    public function test_federation_page(): void
    {
        $sports = OrganizationType::factory()->template('federation')->create(['slug' => 'sports']);
        $fed = Organization::factory()->create(['organization_type_id' => $sports->id, 'name' => ['ar' => 'الاتحاد العماني لكرة القدم'], 'about' => ['ar' => 'نبذة الاتحاد'],
            'founded' => 1978, 'memberships' => [['name' => ['ar' => 'الاتحاد الدولي'], 'year' => 1980]]]);
        $fed->leaders()->create(['kind' => 'president', 'name' => ['ar' => 'رئيس الاتحاد'], 'from_year' => 2019, 'is_current' => true]);
        $fed->milestones()->create(['year' => '2009', 'title' => ['ar' => 'بطل كأس الخليج']]);
        $player = Achiever::factory()->create(['name' => ['ar' => 'علي الحبسي']]);
        $fed->achievers()->attach($player, ['kind' => 'member']);
        Achievement::factory()->create(['title' => ['ar' => 'أفضل حارس']])->achievers()->attach($player);

        $this->get($fed->url())->assertOk()->assertSee('نبذة الاتحاد')->assertSee('الاتحاد الدولي')->assertSee('رئيس الاتحاد')
            ->assertSee('بطل كأس الخليج')->assertSee('أفضل حارس')->assertSee('علي الحبسي');
        $this->get(route('organizations.achievements', $fed))->assertOk()->assertSee('أفضل حارس');
        $this->get(route('organizations.people', $fed))->assertOk()->assertSee('علي الحبسي');
    }
}
