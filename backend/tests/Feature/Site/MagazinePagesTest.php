<?php

namespace Tests\Feature\Site;

use App\Livewire\Site\MagazineIndex;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Issue;
use App\Models\Page;
use App\Models\Writer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MagazinePagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Page::syncFromConfig();
    }

    public function test_magazine_page_shows_issue_lead_rows_most_read_and_writers(): void
    {
        $opinion = ArticleCategory::query()->create(['slug' => 'opinion', 'name' => ['ar' => 'رأي']]);
        $badr = Writer::factory()->create(['name' => ['ar' => 'بدر العلوي']]);
        $lead = Article::factory()->create(['title' => ['ar' => 'ما الذي يبقى'], 'writer_id' => $badr->id, 'article_category_id' => $opinion->id]);
        Article::factory()->create(['title' => ['ar' => 'لا تختصروا الحكاية'], 'views' => 50]);
        Article::factory()->draft()->create(['title' => ['ar' => 'مقال مسودة']]);
        Issue::query()->create(['number' => 12, 'issued_at' => '2026-11-01', 'tagline' => ['ar' => 'أقلام شابة'], 'quote' => ['ar' => 'اقتباس العدد هنا'],
            'quote_cite' => ['ar' => 'بدر العلوي'], 'lead_article_id' => $lead->id]);

        $this->get('/magazine')->assertOk()->assertSee('العدد ١٢')->assertSee('نوفمبر ٢٠٢٦')->assertSee('أقلام شابة')
            ->assertSee('ما الذي يبقى')->assertSee('مقال العدد')->assertSee('اقتباس العدد هنا')->assertSee('لا تختصروا الحكاية')
            ->assertSee('بدر العلوي')->assertSee('مقال واحد')->assertDontSee('مقال مسودة');
    }

    public function test_magazine_list_filters_by_tab_and_search(): void
    {
        $science = ArticleCategory::query()->create(['slug' => 'science', 'name' => ['ar' => 'علوم']]);
        Article::factory()->create(['title' => ['ar' => 'براءة اختراع'], 'article_category_id' => $science->id]);
        Article::factory()->create(['title' => ['ar' => 'الخسارة معلم']]);

        Livewire::test(MagazineIndex::class)->assertSee('براءة اختراع')->assertSee('الخسارة معلم')
            ->call('setTab', 'science')->assertSee('براءة اختراع')->assertDontSee('الخسارة معلم')
            ->call('setTab', '')->dispatch('mag-search', q: 'الخساره')->assertSee('الخسارة معلم')->assertDontSee('براءة اختراع');
        $this->get('/magazine?tab=science')->assertOk()->assertSee('wire:click="setTab(\'science\')"><span>علوم', false)
            ->assertSee('class="chip active" aria-pressed="true" wire:click="setTab(\'science\')"', false);
    }

    public function test_article_page_places_pull_quote_box_and_counts_reads(): void
    {
        $writer = Writer::factory()->create(['name' => ['ar' => 'بدر العلوي'], 'bio' => ['ar' => 'كاتب رأي يكتب عن الأثر.']]);
        $article = Article::factory()->create([
            'slug' => 'applause', 'title' => ['ar' => 'بعد التصفيق'], 'writer_id' => $writer->id, 'lead' => ['ar' => 'الفقرة الأولى'],
            'body' => ['ar' => '<p>مقدمة</p><h2>القسم الأول</h2><p>فقرة أولى</p><p>فقرة ثانية</p><h2>القسم الثاني</h2><p>الخاتمة</p>'],
            'pull_quote' => ['ar' => 'اقتباس بارز'], 'box_title' => ['ar' => 'ثلاثة أسئلة'], 'box_items' => ['ar' => ['سؤال أول', 'سؤال ثان']],
            'tags' => ['ar' => ['الأثر']],
        ]);

        $html = $this->get('/magazine/applause')->assertOk()->assertSee('بعد التصفيق')->assertSee('كاتب رأي يكتب عن الأثر.')
            ->assertSee('#الأثر')->getContent();
        $this->assertMatchesRegularExpression('#فقرة أولى</p><blockquote class="ar-pull">.*اقتباس بارز</blockquote>#u', $html);
        $this->assertMatchesRegularExpression('#فقرة ثانية</p><aside class="ar-box".*سؤال ثان.*</aside><h2>#su', $html);
        $this->assertStringContainsString('<p class="ar-end">الخاتمة', $html);

        $this->post('/magazine/applause/view')->assertNoContent();
        $this->assertSame(1, $article->fresh()->views);
    }

    public function test_draft_article_is_hidden_and_writers_page_lists_published_writers(): void
    {
        Article::factory()->draft()->create(['slug' => 'secret']);
        $this->get('/magazine/secret')->assertNotFound();

        $w = Writer::factory()->create(['slug' => 'laila', 'name' => ['ar' => 'ليلى الرحبي'], 'is_featured' => true, 'quote' => ['ar' => 'قول مأثور']]);
        Article::factory()->create(['title' => ['ar' => 'مقال ليلى'], 'writer_id' => $w->id]);
        Writer::factory()->create(['name' => ['ar' => 'كاتب مسودة'], 'is_published' => false]);

        $this->get('/writers')->assertOk()->assertSee('ليلى الرحبي')->assertSee('قلم العدد')->assertSee('قول مأثور')
            ->assertSee('مقال ليلى')->assertSee('id="laila"', false)->assertDontSee('كاتب مسودة');
    }
}
