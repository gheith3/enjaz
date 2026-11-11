<?php

namespace Tests\Feature\Admin;

use App\Filament\Widgets\ContentHealth;
use App\Models\Achiever;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardWidgetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_content_numbers_and_health(): void
    {
        $this->actingAs($this->userWithRole('editor'));
        Achiever::factory()->create();                    // published, no portrait
        Article::factory()->draft()->create();

        $issues = collect((new ContentHealth)->issues())->pluck('count', 'label');
        $this->assertSame(1, $issues['منجزون منشورون بلا صورة شخصية']);
        $this->assertSame(1, $issues['مقالات مسودة (منها مقالات التصميم التجريبية)']);

        $this->get('/admin')->assertOk()->assertSee('صحة المحتوى')->assertSee('رسائل غير مقروءة');
    }
}
