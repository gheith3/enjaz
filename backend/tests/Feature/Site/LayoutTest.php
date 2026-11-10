<?php

namespace Tests\Feature\Site;

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\SocialLink;
use App\Settings\GeneralSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LayoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Page::syncFromConfig();
    }

    public function test_home_renders_the_design_shell_in_arabic(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<html lang="ar" dir="rtl">', false)
            ->assertSee('<title>الرئيسية | إنجازات شباب عُمان</title>', false)
            ->assertSee('id="vp-lock"', false)
            ->assertSee('<main id="main"', false);
    }

    public function test_stylesheets_follow_the_design_order(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $order = ['main-', 'site-menu-', 'home-banner-', 'desktop-', 'phone-port-home-', 'phone-footer-'];
        $positions = array_map(fn ($name) => strpos($html, 'rel="stylesheet" href="'.url('/build/assets/'.$name)), $order);

        $this->assertNotContains(false, $positions, 'a design stylesheet is missing');
        $sorted = $positions;
        sort($sorted);
        $this->assertSame($sorted, $positions);
    }

    public function test_menus_come_from_the_database(): void
    {
        MenuItem::factory()->create(['label' => ['ar' => 'الإنجازات'], 'label_short' => ['ar' => 'إنجازات'], 'url' => '/achievements', 'show_in_desktop_nav' => true]);
        MenuItem::factory()->create(['label' => ['ar' => 'رابط مخفي'], 'is_active' => false]);
        MenuItem::factory()->create(['location' => 'footer_2', 'label' => ['ar' => 'تواصل معنا'], 'url' => '/contact']);

        $this->get('/')
            ->assertOk()
            ->assertSee('class="site-header has-desktop-nav"', false)
            ->assertSee('>إنجازات</a>', false)       // short label in the desktop bar
            ->assertSee('>الإنجازات</a>', false)     // full label in the menu
            ->assertSee('>تواصل معنا</a>', false)    // footer column
            ->assertDontSee('رابط مخفي');
    }

    public function test_footer_shows_contact_settings_and_social_links(): void
    {
        $settings = app(GeneralSettings::class);
        $settings->phones = ['+968 1234 5678'];
        $settings->email = 'team@example.om';
        $settings->copyright = ['ar' => 'جميع الحقوق محفوظة'];
        $settings->save();
        SocialLink::create(['platform' => 'instagram', 'url' => 'https://instagram.com/example', 'sort_order' => 1]);
        SocialLink::create(['platform' => 'youtube', 'url' => null, 'sort_order' => 2]);

        $this->get('/')
            ->assertOk()
            ->assertSee('href="tel:+96812345678"', false)
            ->assertSee('mailto:team@example.om', false)
            ->assertSee('جميع الحقوق محفوظة')
            ->assertSee('href="https://instagram.com/example"', false)
            ->assertSee('class="footer-social-unavailable"', false);
    }

    public function test_page_texts_are_editable(): void
    {
        $page = Page::query()->where('key', 'home')->firstOrFail();
        $page->setTranslation('title', 'ar', 'بداية الموقع')->setTranslation('meta_description', 'ar', 'وصف للمشاركة')->save();

        $this->get('/')
            ->assertSee('<title>بداية الموقع | إنجازات شباب عُمان</title>', false)
            ->assertSee('<meta name="description" content="وصف للمشاركة">', false);
    }
}
