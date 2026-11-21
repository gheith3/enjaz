<?php

namespace Tests\Feature\Site;

use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\Article;
use App\Models\Award;
use App\Models\LabItem;
use App\Models\LeaderQuote;
use App\Models\Organization;
use App\Models\OrganizationType;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Writer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Page::syncFromConfig();
    }

    private function section(string $key): PageSection
    {
        return PageSection::query()->where('key', $key)->firstOrFail();
    }

    public function test_home_renders_its_sections_from_the_database(): void
    {
        LeaderQuote::query()->create(['honorific' => ['ar' => 'حضرة صاحب الجلالة'], 'name' => ['ar' => 'السلطان هيثم'], 'lead' => ['ar' => 'إن الشباب هم ثروة الأمم'], 'body' => ['ar' => 'وموردها']]);
        Achiever::factory()->create(['name' => ['ar' => 'منجز الشريط'], 'show_in_home_strip' => true]);
        Achievement::factory()->create(['title' => ['ar' => 'إنجاز طويل'], 'home_title' => ['ar' => 'جائزة مان بوكر'], 'show_on_home' => true, 'home_slot' => 'feature']);
        Award::factory()->create(['name' => ['ar' => 'جائزة السلطان قابوس للعمل التطوعي'], 'subtitle' => ['ar' => 'للعمل التطوعي'], 'card_style' => 'vol', 'motto' => ['ar' => 'تطوعي حياة']]);
        $sports = $this->section('home.sports');
        $sports->items()->create(['title' => ['ar' => 'غلام خميس'], 'label' => ['ar' => 'كرة القدم'], 'url' => 'https://example.com/ghulam']);
        $gov = OrganizationType::factory()->create(['slug' => 'gov']);
        Organization::factory()->create(['name' => ['ar' => 'وزارة الثقافة'], 'organization_type_id' => $gov->id, 'is_partner' => true, 'is_published' => false]);
        $this->section('home.stats')->forceFill(['settings' => ['stats' => [['value' => 221, 'label' => ['ar' => 'عدد المنجزين']]]]])->save();

        $this->get('/')->assertOk()
            ->assertSee('إن الشباب هم ثروة الأمم')->assertSee('منجز الشريط')->assertSee('جائزة مان بوكر')->assertDontSee('إنجاز طويل')
            ->assertSee('data-n="221"', false)->assertSee('<h2 id="hs-title">عُمان <em>تُنجز</em></h2>', false)->assertSee('للعمل التطوعي')->assertSee('جائزة السلطان قابوس')->assertSee('«تطوعي حياة»')
            ->assertSee('غلام خميس')->assertSee('href="https://example.com/ghulam"', false)->assertSee('sports-motion-ali', false)
            ->assertSee('title="وزارة الثقافة"', false);
    }

    public function test_disabled_sections_are_hidden(): void
    {
        LeaderQuote::query()->create(['name' => ['ar' => 'اسم'], 'lead' => ['ar' => 'كلمة الهيدر']]);
        $this->section('home.hero')->update(['is_enabled' => false]);

        $this->get('/')->assertOk()->assertDontSee('كلمة الهيدر');
    }

    public function test_the_articles_section_shows_five_articles_with_home_picks_first(): void
    {
        $writer = Writer::factory()->create(['name' => ['ar' => 'ليلى الرحبي'], 'role' => ['ar' => 'كاتبة رأي']]);
        Article::factory()->create(['title' => ['ar' => 'مقال مختار للرئيسية'], 'show_on_home' => true, 'writer_id' => $writer->id, 'published_at' => now()->subDays(20)]);
        foreach (range(1, 5) as $i) {
            Article::factory()->create(['title' => ['ar' => 'مقال حديث رقم '.$i], 'published_at' => now()->subDays($i)]);
        }

        $this->get('/')->assertOk()
            ->assertSeeInOrder(['مقال مختار للرئيسية', 'مقال حديث رقم 1', 'مقال حديث رقم 2', 'مقال حديث رقم 3', 'مقال حديث رقم 4'])
            ->assertDontSee('مقال حديث رقم 5')
            ->assertSee('<b>ليلى الرحبي</b><small>كاتبة رأي</small>', false)
            ->assertSee('class="am-all" href="'.route('magazine.index').'"', false);
    }

    public function test_the_science_section_shows_four_expanding_panels_with_medals(): void
    {
        foreach (range(1, 4) as $i) {
            LabItem::factory()->create(['kind' => 'invention', 'title' => ['ar' => 'ابتكار رقم '.$i], 'who' => ['ar' => 'مبتكر '.$i], 'medal' => $i === 1 ? 'silver' : 'gold',
                'happened_at' => now()->subMonths($i), 'published_at' => now()->subMonths($i)]);
        }

        $this->get('/')->assertOk()
            ->assertSee('data-sx', false)
            ->assertSee('class="sx-p is-on"', false)
            ->assertSeeInOrder(['ابتكار رقم 4', 'ابتكار رقم 1', 'ابتكار رقم 2', 'ابتكار رقم 3'])
            ->assertSee('class="sx-medal silver"', false)->assertSee('مبتكر 1');
    }
}
