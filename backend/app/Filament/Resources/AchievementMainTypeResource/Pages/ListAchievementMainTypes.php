<?php

namespace App\Filament\Resources\AchievementMainTypeResource\Pages;

use App\Filament\Resources\AchievementMainTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAchievementMainTypes extends ListRecords
{
    protected static string $resource = AchievementMainTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
