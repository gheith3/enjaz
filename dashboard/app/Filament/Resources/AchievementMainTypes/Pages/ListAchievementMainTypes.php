<?php

namespace App\Filament\Resources\AchievementMainTypes\Pages;

use App\Filament\Resources\AchievementMainTypes\AchievementMainTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAchievementMainTypes extends ListRecords
{
    protected static string $resource = AchievementMainTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus'),
        ];
    }
}
