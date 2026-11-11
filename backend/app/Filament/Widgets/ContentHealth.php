<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Achievements\AchievementResource;
use App\Filament\Resources\Achievers\AchieverResource;
use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\LabItems\LabItemResource;
use App\Filament\Resources\News\NewsResource;
use App\Filament\Resources\Organizations\OrganizationResource;
use App\Filament\Resources\Writers\WriterResource;
use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\Article;
use App\Models\LabItem;
use App\Models\News;
use App\Models\Organization;
use App\Models\Writer;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;

/**
 * Dashboard «صحة المحتوى»: what is still to do — published records without their main image, and drafts waiting
 * (imported placeholders included). Each line links to the list.
 */
class ContentHealth extends Widget
{
    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.content-health';

    /** @return list<array{label: string, count: int, url: string, level: string}> */
    public function issues(): array
    {
        $missing = fn (string $model, string $collection) => $model::query()->where('is_published', true)
            ->whereDoesntHave('media', fn (Builder $q) => $q->where('collection_name', $collection))->count();
        $drafts = fn (string $model) => $model::query()->where('is_published', false)->count();

        $rows = [
            ['منجزون منشورون بلا صورة شخصية', $missing(Achiever::class, 'portrait'), AchieverResource::getUrl('index'), 'warning'],
            ['إنجازات منشورة بلا صورة غلاف', $missing(Achievement::class, 'cover'), AchievementResource::getUrl('index'), 'warning'],
            ['مؤسسات منشورة بلا شعار', $missing(Organization::class, 'logo'), OrganizationResource::getUrl('index'), 'warning'],
            ['عناصر مختبر منشورة بلا صورة', $missing(LabItem::class, 'cover'), LabItemResource::getUrl('index'), 'warning'],
            ['مقالات منشورة بلا صورة غلاف (تظهر بعلامة «)', $missing(Article::class, 'cover'), ArticleResource::getUrl('index'), 'info'],
            ['إنجازات مسودة (لا تظهر في الموقع)', $drafts(Achievement::class), AchievementResource::getUrl('index'), 'info'],
            ['أخبار مسودة (منها أخبار التصميم التجريبية)', $drafts(News::class), NewsResource::getUrl('index'), 'info'],
            ['مقالات مسودة (منها مقالات التصميم التجريبية)', $drafts(Article::class), ArticleResource::getUrl('index'), 'info'],
            ['كتّاب مسودة', $drafts(Writer::class), WriterResource::getUrl('index'), 'info'],
            ['مؤسسات مسودة (منها شعارات الشركاء)', $drafts(Organization::class), OrganizationResource::getUrl('index'), 'info'],
        ];

        return collect($rows)->filter(fn ($r) => $r[1] > 0)
            ->map(fn ($r) => ['label' => $r[0], 'count' => $r[1], 'url' => $r[2], 'level' => $r[3]])->values()->all();
    }
}
