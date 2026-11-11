<?php

namespace Tests\Feature\Site;

use App\Models\Achiever;
use App\Models\News;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InspiringPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Page::syncFromConfig();
    }

    public function test_inspiring_page_shows_people_fields_and_himam(): void
    {
        Achiever::factory()->create(['name' => ['ar' => 'محمد المشايخي'], 'is_inspiring' => true, 'inspiring_field' => ['ar' => 'الرياضة'],
            'inspiring_tagline' => ['ar' => 'حين تصبح القوة قراراً يومياً.']]);
        Achiever::factory()->create(['name' => ['ar' => 'غير ملهم'], 'is_inspiring' => false]);
        $himam = PageSection::query()->where('key', 'inspiring.himam')->firstOrFail();
        $himam->forceFill(['settings' => ['title' => ['ar' => 'هِمَمٌ|بلا حدود']]])->save();
        $himam->items()->create(['title' => ['ar' => 'فضية العالم بلندن'], 'label' => ['ar' => 'رياضة'], 'url' => '/about', 'settings' => ['meta' => ['ar' => 'أغسطس ٢٠١٧']]]);
        News::factory()->create(['title' => ['ar' => 'خبر همم'], 'show_in_himam' => true, 'is_published' => true]);

        $this->get('/inspiring')->assertOk()->assertSee('محمد المشايخي')->assertSee('حين تصبح القوة قراراً يومياً.')
            ->assertSee('data-field="الرياضة"', false)->assertDontSee('غير ملهم')
            ->assertSee('<em>بلا حدود</em>', false)->assertSee('فضية العالم بلندن')->assertSee('أغسطس ٢٠١٧')->assertSee('خبر همم');
    }
}
