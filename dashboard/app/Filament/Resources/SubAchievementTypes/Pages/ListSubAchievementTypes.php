<?php

namespace App\Filament\Resources\SubAchievementTypes\Pages;

use App\Filament\Resources\SubAchievementTypes\SubAchievementTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSubAchievementTypes extends ListRecords
{
    protected static string $resource = SubAchievementTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus'),
        ];
    }
}
