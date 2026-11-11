<?php

namespace Tests\Feature\Site;

use App\Models\Achiever;
use App\Models\Article;
use App\Models\LabItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_finds_published_records_ignoring_hamza_and_ta_marbuta(): void
    {
        Achiever::factory()->create(['name' => ['ar' => 'جوخة الحارثية']]);
        Achiever::factory()->create(['name' => ['ar' => 'جوخة مسودة'], 'is_published' => false]);
        Article::factory()->create(['title' => ['ar' => 'الإرادة لا تستأذن أحداً']]);
        LabItem::factory()->create(['title' => ['ar' => 'نظارات مساندة'], 'short' => ['ar' => 'نظارات']]);

        $this->get('/search?q='.urlencode('جوخه'))->assertOk()->assertSee('جوخة الحارثية')->assertDontSee('جوخة مسودة')
            ->assertSee('<meta name="robots" content="noindex">', false);
        $this->get('/search?q='.urlencode('الاراده'))->assertOk()->assertSee('الإرادة لا تستأذن أحداً');
        $this->get('/search?q='.urlencode('نظارات'))->assertOk()->assertSee('نظارات مساندة');
        $this->get('/search?q=x')->assertOk()->assertSee(__('site.search_page.hint'));
    }
}
