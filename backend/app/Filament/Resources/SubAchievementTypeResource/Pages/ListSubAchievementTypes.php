<?php

namespace App\Filament\Resources\SubAchievementTypeResource\Pages;

use App\Filament\Resources\SubAchievementTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubAchievementTypes extends ListRecords
{
    protected static string $resource = SubAchievementTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
