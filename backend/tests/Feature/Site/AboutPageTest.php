<?php

namespace Tests\Feature\Site;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_prints_its_texts_and_the_script_data(): void
    {
        Page::syncFromConfig();
        $page = Page::forKey('about');
        $page->forceFill(['hero_title' => ['ar' => 'نوثّق |إنجازات| شباب عُمان'], 'extra' => ['mission' => ['ar' => 'نص الرسالة'], 'fact1_n' => ['ar' => '٢٠١٤'], 'fact1_t' => ['ar' => 'البداية']]])->save();
        $team = PageSection::query()->where('key', 'about.team')->first();
        $team->items()->create(['title' => ['ar' => 'خميس السلطي'], 'label' => ['ar' => 'عضو']]);
        $journey = PageSection::query()->where('key', 'about.journey')->first();
        $journey->items()->create(['title' => ['ar' => 'إطلاق الموقع'], 'label' => ['ar' => 'يناير'], 'settings' => ['meta' => ['ar' => '٢٠١٨']]]);
        PageSection::query()->where('key', 'about.media')->update(['is_enabled' => false]);

        $html = $this->get('/about')->assertOk()->assertSee('<em>إنجازات</em>', false)->assertSee('نص الرسالة')->assertSee('البداية')
            ->assertSee('class="page"', false)->assertSee('class="header', false)->getContent();
        $this->assertStringContainsString('"team":[{"n":"خميس السلطي","r":"عضو","img":""}]', $html);
        $this->assertStringContainsString('"roadmap":[{"y":"٢٠١٨","date":"يناير","s":"إطلاق الموقع"', $html);
        $this->assertStringNotContainsString('"#media"', $html);
        $this->assertMatchesRegularExpression('/<section\s+hidden\s+class="sec wrap reveal" id="media"/', $html);
        $this->assertStringNotContainsString('main-', collect(explode('<link', $html))->filter(fn ($l) => str_contains($l, 'stylesheet'))->implode(''));
    }
}
