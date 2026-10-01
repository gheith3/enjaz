<?php

namespace App\Filament\Resources\AchievementScopes\Pages;

use App\Filament\Resources\AchievementScopes\AchievementScopeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAchievementScopes extends ListRecords
{
    protected static string $resource = AchievementScopeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus'),
        ];
    }
}
