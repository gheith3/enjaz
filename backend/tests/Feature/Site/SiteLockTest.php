<?php

namespace Tests\Feature\Site;

use App\Models\Achiever;
use App\Models\Page;
use App\Models\ProfileReview;
use App\Settings\SiteLockSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteLockTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Page::syncFromConfig();
    }

    private function lock(?string $code = '123456'): void
    {
        $settings = app(SiteLockSettings::class);
        $settings->enabled = true;
        $settings->code = $code;
        $settings->save();
    }

    public function test_site_is_open_when_the_lock_is_off(): void
    {
        $this->get('/')->assertOk()->assertDontSee('من أجل عُمان وأبنائها المبدعين');
    }

    public function test_locked_site_shows_the_coming_soon_page_to_visitors(): void
    {
        $this->lock();

        $this->get('/')->assertOk()->assertSee('من أجل عُمان وأبنائها المبدعين')->assertSee('للمبدعين والمنجزين العُمانيين')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->get('/achievers')->assertOk()->assertSee('من أجل عُمان وأبنائها المبدعين');
    }

    public function test_the_right_code_opens_the_site_and_a_wrong_one_does_not(): void
    {
        $this->lock();

        $this->from('/')->post('/unlock', ['code' => '000000'])->assertRedirect('/')->assertSessionHasErrors('code');
        $this->post('/unlock', ['code' => '12345'])->assertSessionHasErrors('code');

        $cookie = $this->post('/unlock', ['code' => '123456'])->assertRedirect('/')->getCookie('injazat_pass', false);
        $this->assertNotNull($cookie);

        $this->withUnencryptedCookie('injazat_pass', $cookie->getValue())
            ->get('/')->assertOk()->assertDontSee('من أجل عُمان وأبنائها المبدعين');
    }

    public function test_dashboard_users_and_review_links_pass_the_lock(): void
    {
        $this->lock();
        $review = ProfileReview::start(Achiever::factory()->create());

        $this->get($review->url())->assertOk()->assertSee('الاسم الكامل');
        $this->get('/admin/login')->assertOk();

        $this->actingAs($this->userWithRole('editor'));
        $this->get('/')->assertOk()->assertDontSee('من أجل عُمان وأبنائها المبدعين');
    }
}
