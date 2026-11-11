<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\LeaderQuotes\LeaderQuoteResource;
use App\Filament\Resources\PageSections\PageSectionResource;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_open_leader_quotes_and_every_home_section(): void
    {
        $this->actingAs($this->userWithRole('editor'));
        Page::syncFromConfig();

        $this->get(LeaderQuoteResource::getUrl('index'))->assertOk();
        foreach (PageSection::query()->where('key', 'like', 'home.%')->get() as $section) {
            $this->get(PageSectionResource::getUrl('edit', ['record' => $section]))->assertOk();
        }
        $sports = PageSection::query()->where('key', 'home.sports')->first();
        $this->get(PageSectionResource::getUrl('edit', ['record' => $sports]))->assertSee('الرياضة');
    }
}
