<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\ArticleCategories\ArticleCategoryResource;
use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\Issues\IssueResource;
use App\Filament\Resources\LabItems\LabItemResource;
use App\Filament\Resources\NewsCategories\NewsCategoryResource;
use App\Filament\Resources\Pages\PageResource;
use App\Filament\Resources\Writers\WriterResource;
use App\Models\Article;
use App\Models\LabItem;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase4DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_open_the_lab_news_and_magazine_screens(): void
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
        $article = Article::factory()->create();
        foreach ([ArticleResource::getUrl('index'), ArticleResource::getUrl('create'), ArticleResource::getUrl('edit', ['record' => $article]),
            WriterResource::getUrl('index'), WriterResource::getUrl('create'), IssueResource::getUrl('index'), ArticleCategoryResource::getUrl('index')] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get(PageResource::getUrl('edit', ['record' => Page::query()->where('key', 'lab')->first()]))->assertOk()->assertSee('قاعة العرض');
    }
}
