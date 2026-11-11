<?php

namespace Tests\Feature\Site;

use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\Award;
use App\Models\LeaderQuote;
use App\Models\Organization;
use App\Models\OrganizationType;
use App\Models\Page;
use App\Models\PageSection;
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
            ->assertSee('data-n="221"', false)->assertSee('للعمل التطوعي')->assertSee('جائزة السلطان قابوس')->assertSee('«تطوعي حياة»')
            ->assertSee('غلام خميس')->assertSee('href="https://example.com/ghulam"', false)->assertSee('sports-motion-ali', false)
            ->assertSee('title="وزارة الثقافة"', false);
    }

    public function test_disabled_sections_are_hidden(): void
    {
        LeaderQuote::query()->create(['name' => ['ar' => 'اسم'], 'lead' => ['ar' => 'كلمة الهيدر']]);
        $this->section('home.hero')->update(['is_enabled' => false]);

        $this->get('/')->assertOk()->assertDontSee('كلمة الهيدر');
    }
}
