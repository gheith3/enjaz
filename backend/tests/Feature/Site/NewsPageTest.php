<?php

namespace Tests\Feature\Site;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Organization;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Page::syncFromConfig();
    }

    public function test_news_page_shows_the_published_news_of_the_category_in_full(): void
    {
        $science = NewsCategory::query()->create(['slug' => 'science', 'name' => ['ar' => 'الابتكارات العلمية'], 'intro' => ['ar' => 'أخبار القسم العلمي']]);
        $sport = NewsCategory::query()->create(['slug' => 'sport', 'name' => ['ar' => 'الرياضة'], 'sort_order' => 2]);
        $water = News::factory()->create(['slug' => 'water', 'news_category_id' => $science->id, 'title' => ['ar' => 'تنقية المياه'],
            'body' => ['ar' => '<p>نص الخبر كاملًا</p>'], 'is_published' => true]);
        News::factory()->create(['news_category_id' => $science->id, 'title' => ['ar' => 'خبر مسودة'], 'is_published' => false]);
        News::factory()->create(['news_category_id' => $sport->id, 'title' => ['ar' => 'خبر رياضي'], 'is_published' => true]);

        $this->get('/news?category=science')->assertOk()->assertSee('الابتكارات العلمية')->assertSee('أخبار القسم العلمي')
            ->assertSee('تنقية المياه')->assertSee('نص الخبر كاملًا')->assertSee('id="news-water"', false)
            ->assertDontSee('خبر مسودة')->assertDontSee('خبر رياضي');
        $this->get('/news')->assertOk()->assertSee('تنقية المياه');
        $this->get('/news?category=sport')->assertOk()->assertSee('خبر رياضي')->assertDontSee('تنقية المياه');
        $this->assertStringEndsWith('/news?category=science#news-water', $water->url());
    }

    public function test_organization_news_keep_their_own_page(): void
    {
        $news = News::factory()->create(['organization_id' => Organization::factory()->create()->id, 'is_published' => true]);

        $this->assertSame(route('news.show', $news), $news->url());
        $this->get($news->url())->assertOk();
    }
}
