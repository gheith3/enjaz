<?php

namespace Tests\Feature\Import;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Issue;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Writer;

/** Design import: News section and magazine (drafts). */
class ImportNewsMagazineTest extends ImportTestCase
{
    public function test_it_imports_the_news_section_as_drafts(): void
    {
        $this->artisan('injazat:import-design', ['--only' => ['news']])->assertSuccessful();

        $science = NewsCategory::query()->where('slug', 'science')->firstOrFail();
        $this->assertSame('الابتكارات والاختراعات والمنشورات العلمية', $science->name);
        $this->assertSame(3, $science->news()->count());
        $this->assertSame(0, $science->news()->where('is_published', true)->count());
        $this->assertTrue(News::query()->where('slug', 'water')->first()->hasMedia('cover'));
    }

    public function test_it_imports_the_magazine_as_drafts(): void
    {
        $this->artisan('injazat:import-design', ['--only' => ['magazine']])->assertSuccessful();

        $this->assertSame(8, Writer::query()->count());
        $this->assertSame(12, Article::query()->count());
        $this->assertSame(0, Article::query()->where('is_published', true)->count());
        $issue = Issue::query()->where('number', 12)->firstOrFail();
        $lead = $issue->leadArticle;
        $this->assertSame('ما الذي يبقى بعد انتهاء التصفيق؟', $lead->title);
        $this->assertSame('بدر العلوي', $lead->writer->name);
        $this->assertCount(3, $lead->boxItems());
        $this->assertStringContainsString('<h2>من الميدالية إلى المهارة</h2>', $lead->body);
        $this->assertTrue(Writer::query()->where('slug', 'badr')->first()->is_featured);
        $this->assertSame(7, Article::query()->where('slug', 'patent')->first()->read_minutes);
        $this->assertSame(5, ArticleCategory::query()->count());
    }
}
