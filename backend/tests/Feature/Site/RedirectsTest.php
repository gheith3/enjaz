<?php

namespace Tests\Feature\Site;

use App\Models\Page;
use App\Models\Redirect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedirectsTest extends TestCase
{
    use RefreshDatabase;

    public function test_old_design_address_redirects_and_counts_hits(): void
    {
        Redirect::create(['from_path' => '/achievers.html', 'to_path' => '/achievers']);

        $this->get('/achievers.html')->assertRedirect('/achievers')->assertStatus(301);

        $this->assertSame(1, Redirect::first()->hits);
    }

    public function test_query_string_match_wins_over_bare_path(): void
    {
        Redirect::create(['from_path' => '/federation.html', 'to_path' => '/organizations']);
        Redirect::create(['from_path' => '/federation.html?org=oman-fa', 'to_path' => '/organizations/oman-fa', 'status_code' => 302]);

        $this->get('/federation.html?org=oman-fa')->assertRedirect('/organizations/oman-fa')->assertStatus(302);
        $this->get('/federation.html?org=other')->assertRedirect('/organizations');
    }

    public function test_unknown_address_shows_the_designed_404(): void
    {
        Page::syncFromConfig();

        $this->get('/no-such-page')->assertNotFound()->assertSee('الصفحة غير موجودة')->assertSee('class="site-header', false);
    }
}
