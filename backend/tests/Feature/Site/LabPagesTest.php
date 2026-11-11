<?php

namespace Tests\Feature\Site;

use App\Models\LabItem;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LabPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Page::syncFromConfig();
    }

    public function test_lab_page_shows_published_inventions_and_publications(): void
    {
        LabItem::factory()->create(['title' => ['ar' => 'ذهبية النظارات'], 'short' => ['ar' => 'نظارات مساندة'], 'who' => ['ar' => 'نهية الحمدانية']]);
        LabItem::factory()->publication()->create(['title' => ['ar' => 'محفز الهيدروجين'], 'finding_value' => ['ar' => '٩٥٪']]);
        LabItem::factory()->draft()->create(['title' => ['ar' => 'ابتكار مسودة']]);
        Page::forKey('lab')->forceFill(['extra' => ['stat_inventions' => ['ar' => '62'], 'cta_title' => ['ar' => 'لديك اختراع؟']]])->save();

        $this->get('/lab')->assertOk()->assertSee('نظارات مساندة')->assertSee('نهية الحمدانية')->assertSee('محفز الهيدروجين')
            ->assertSee('٩٥٪')->assertSee('data-count="62"', false)->assertSee('لديك اختراع؟')->assertDontSee('ابتكار مسودة');
    }

    public function test_all_inventions_filters_by_medal_and_year(): void
    {
        LabItem::factory()->create(['short' => ['ar' => 'ابتكار ذهبي'], 'medal' => 'gold', 'happened_at' => '2026-05-01']);
        LabItem::factory()->create(['short' => ['ar' => 'ابتكار فضي'], 'medal' => 'silver', 'happened_at' => '2025-04-01']);
        LabItem::factory()->publication()->create(['title' => ['ar' => 'بحث منشور']]);

        $this->get('/lab/inventions')->assertOk()->assertSee('ابتكار ذهبي')->assertSee('ابتكار فضي')->assertDontSee('بحث منشور');
        $this->get('/lab/inventions?medal=silver')->assertOk()->assertSee('ابتكار فضي')->assertDontSee('ابتكار ذهبي');
        $this->get('/lab/inventions?year=2026')->assertOk()->assertSee('ابتكار ذهبي')->assertDontSee('ابتكار فضي');
        $this->get('/lab/publications')->assertOk()->assertSee('بحث منشور')->assertDontSee('ابتكار ذهبي');
    }

    public function test_invention_and_publication_pages(): void
    {
        $inv = LabItem::factory()->create(['slug' => 'senseglass', 'title' => ['ar' => 'ذهبية ITEX'], 'body' => ['ar' => '<h2>الفكرة</h2><p>نص القصة</p>'],
            'credit_text' => 'الرؤية', 'credit_url' => 'https://alroya.om/x']);
        $inv->facts()->create(['group' => 'facts', 'label' => ['ar' => 'الميدالية'], 'value' => ['ar' => 'ذهبية']]);
        $inv->sources()->create(['title' => ['ar' => 'خبر الرؤية'], 'url' => 'https://alroya.om/x']);
        $pub = LabItem::factory()->publication()->create(['slug' => 'carbon-ai', 'title' => ['ar' => 'احتجاز الكربون'], 'happened_at' => '2026-06-01']);

        $this->get('/lab/senseglass')->assertOk()->assertSee('ذهبية ITEX')->assertSee('الفكرة')->assertSee('بطاقة الابتكار')
            ->assertSee('خبر الرؤية')->assertSee('https://alroya.om/x', false);
        $this->get('/lab/carbon-ai')->assertOk()->assertSee('احتجاز الكربون')->assertSee('ملخص البحث')
            ->assertSee('data-cite="باحث عُماني، باحث آخر (2026). احتجاز الكربون. مجلة علمية."', false);
    }

    public function test_draft_item_is_hidden_from_visitors(): void
    {
        $draft = LabItem::factory()->draft()->create(['slug' => 'secret']);

        $this->get('/lab/secret')->assertNotFound();
        $this->actingAs($this->userWithRole('editor'))->get($draft->url())->assertOk();
    }
}
