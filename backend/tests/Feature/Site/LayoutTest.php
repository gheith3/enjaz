<?php

namespace Tests\Feature\Site;

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\SocialLink;
use App\Settings\GeneralSettings;
use App\Support\Site\CssBundles;
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
        // The home page links its merged stylesheet, which keeps the design's order inside.
        $this->get('/')->assertOk()->assertSee('rel="stylesheet" href="'.url('/build/assets/home-'), false);
        $bundle = file_get_contents(base_path(CssBundles::path('home')));

        $order = ['main', 'site-menu', 'home-banner', 'desktop', 'phone-port-home', 'phone-footer', 'polish-global', 'polish-home'];
        $positions = array_map(fn ($name) => strpos($bundle, '"../css/'.$name.'.css"'), $order);

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
            ->assertSee("'x-navfit2:' + innerWidth", false)   // تصاميم ٢٠٤٠: section sizes set before the first paint
            ->assertSee('>إنجازات</a>', false)       // short label in the desktop bar
            ->assertSee('>الإنجازات</a>', false)     // full label in the menu
            ->assertSee('>تواصل معنا</a>', false)    // footer column
            ->assertDontSee('رابط مخفي');
    }

    public function test_pages_load_one_stylesheet_and_no_banner_intro_on_internal_navigation(): void
    {
        $html = $this->get('/achievements')->assertOk()->getContent();

        // One merged stylesheet for the page (resources/site/css-bundles), not one per design file.
        $links = collect(explode('<link', $html))->filter(fn ($l) => str_contains($l, 'rel="stylesheet"'));
        $this->assertCount(1, $links);
        // Internal navigation: banners complete without replaying their intro; it runs before the stylesheet.
        $this->assertStringContainsString('<script id="x-instant">', $html);
        $this->assertLessThan(strpos($html, '<link rel="stylesheet"'), strpos($html, 'x-instant'));
        // Nothing holds the first paint or loads pages in the background (they made the site feel slow).
        $this->assertStringNotContainsString('blocking="render"', $html);
        $this->assertStringNotContainsString('speculationrules', $html);
        $this->assertStringNotContainsString('requestAnimationFrame(loop)', $html);
    }

    public function test_every_page_has_its_stylesheet_bundle_in_sync_with_the_config(): void
    {
        foreach (CssBundles::keys() as $key) {
            $path = base_path(CssBundles::path($key));
            $this->assertFileExists($path, 'Run php artisan site:css-bundles');
            $this->assertSame(CssBundles::contents($key), file_get_contents($path), CssBundles::name($key).'.css is out of date: run php artisan site:css-bundles');
        }
        $this->assertCount(count(CssBundles::keys()), glob(base_path(CssBundles::DIR.'/*.css')));

        // Same files in the same order as before the merge; about.html has no main.css.
        $this->assertStringStartsWith('/* Generated', CssBundles::contents('home'));
        $this->assertStringContainsString("@import \"../css/main.css\";\n@import \"../css/site-menu.css\";", CssBundles::contents('home'));
        $this->assertStringEndsWith("@import \"../css/polish-home.css\";\n", CssBundles::contents('home'));
        $this->assertStringNotContainsString('main.css', CssBundles::contents('about'));
        $this->assertSame([CssBundles::path('about')], CssBundles::for('about'));
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
