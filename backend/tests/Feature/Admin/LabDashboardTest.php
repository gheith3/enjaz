<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\LabItems\LabItemResource;
use App\Filament\Resources\NewsCategories\NewsCategoryResource;
use App\Filament\Resources\Pages\PageResource;
use App\Models\LabItem;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LabDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_open_the_lab_screens(): void
    {
        $this->actingAs($this->userWithRole('editor'));
        Page::syncFromConfig();
        $inv = LabItem::factory()->create();
        $pub = LabItem::factory()->publication()->create();

        foreach ([LabItemResource::getUrl('index'), LabItemResource::getUrl('create'), LabItemResource::getUrl('edit', ['record' => $inv]),
            LabItemResource::getUrl('edit', ['record' => $pub])] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get(NewsCategoryResource::getUrl('index'))->assertOk();
        $this->get(PageResource::getUrl('edit', ['record' => Page::query()->where('key', 'lab')->first()]))->assertOk()->assertSee('قاعة العرض');
    }
}
