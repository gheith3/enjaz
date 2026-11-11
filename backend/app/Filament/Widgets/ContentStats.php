<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Achievements\AchievementResource;
use App\Filament\Resources\Achievers\AchieverResource;
use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\Organizations\OrganizationResource;
use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\Article;
use App\Models\ContactMessage;
use App\Models\Organization;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/** Dashboard: published / draft counts of the main content, and unread contact messages. */
class ContentStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $stat = function (string $label, string $model, string $resource, string $icon) {
            $published = $model::query()->where('is_published', true)->count();
            $drafts = $model::query()->where('is_published', false)->count();

            return Stat::make($label, number_format($published))->description($drafts ? "مسودات: $drafts" : 'لا مسودات')
                ->icon($icon)->url($resource::getUrl('index'));
        };
        $unread = ContactMessage::query()->whereNull('read_at')->count();

        return [
            $stat('المنجزون', Achiever::class, AchieverResource::class, 'heroicon-o-user-group'),
            $stat('الإنجازات', Achievement::class, AchievementResource::class, 'heroicon-o-star'),
            $stat('المؤسسات', Organization::class, OrganizationResource::class, 'heroicon-o-building-office-2'),
            $stat('المقالات', Article::class, ArticleResource::class, 'heroicon-o-pencil-square'),
            Stat::make('رسائل غير مقروءة', (string) $unread)->icon('heroicon-o-inbox')->color($unread ? 'warning' : 'gray')
                ->url(ContactMessageResource::getUrl('index')),
        ];
    }
}
